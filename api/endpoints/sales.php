<?php
// Muestra errores para depuración. ¡Desactiva esto en un entorno de producción!
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$pdo = require '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// Manejar la solicitud pre-vuelo OPTIONS para CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Este endpoint solo acepta el método POST
if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido.']);
    exit();
}

// --- Lógica Principal para Registrar Venta ---

// 1. Leer los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// 2. Validación básica de los datos de entrada
if (!isset($data['total_amount']) || !isset($data['items']) || empty($data['items'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Datos de venta incompletos o inválidos.']);
    exit;
}

// 3. Iniciar la transacción. ¡CRÍTICO!
$pdo->beginTransaction();

try {
    // --- PASO A: Insertar el registro principal de la venta ---
    $stmt = $pdo->prepare(
        "INSERT INTO sales (total_amount, payment_method) VALUES (?, ?)"
    );
    $stmt->execute([
        $data['total_amount'],
        $data['payment_method']
    ]);
    $sale_id = $pdo->lastInsertId();

    // --- PASO B: Procesar cada ítem de la venta (Lógica FIFO) ---
    foreach ($data['items'] as $item) {
        $product_id = $item['product_id'];
        $quantity_to_sell = $item['quantity'];
        $sale_price = $item['sale_price_per_unit'];

        // Buscar lotes con stock disponible para este producto, ordenados por fecha (FIFO)
        $find_lots_stmt = $pdo->prepare("
            SELECT
                poi.id,
                poi.quantity_received,
                poi.quantity_sold
            FROM purchase_order_items poi
            JOIN purchase_orders po ON poi.purchase_order_id = po.id
            WHERE poi.product_id = ? AND poi.quantity_received > poi.quantity_sold
            ORDER BY po.order_date ASC, poi.created_at ASC
        ");
        $find_lots_stmt->execute([$product_id]);
        $available_lots = $find_lots_stmt->fetchAll();

        if (empty($available_lots)) {
            throw new Exception("Stock agotado para el producto ID: $product_id");
        }

        // Iterar sobre los lotes disponibles para satisfacer la cantidad de la venta
        foreach ($available_lots as $lot) {
            if ($quantity_to_sell <= 0) break; // Si ya se completó la cantidad, salir

            $stock_in_this_lot = $lot['quantity_received'] - $lot['quantity_sold'];
            $quantity_from_this_lot = min($quantity_to_sell, $stock_in_this_lot);

            // Actualizar el stock vendido en el lote de compra
            $update_poi_stmt = $pdo->prepare(
                "UPDATE purchase_order_items SET quantity_sold = quantity_sold + ? WHERE id = ?"
            );
            $update_poi_stmt->execute([$quantity_from_this_lot, $lot['id']]);

            // Registrar el detalle de esta venta (de qué lote salió)
            $insert_si_stmt = $pdo->prepare(
                "INSERT INTO sale_items (sale_id, product_id, purchase_order_item_id, quantity_sold, sale_price_per_unit) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $insert_si_stmt->execute([
                $sale_id,
                $product_id,
                $lot['id'], // ¡Importante! Referencia al lote
                $quantity_from_this_lot,
                $sale_price
            ]);
            
            // Reducir la cantidad que aún falta por vender
            $quantity_to_sell -= $quantity_from_this_lot;
        }

        // Si después de revisar todos los lotes aún falta cantidad, no hay suficiente stock
        if ($quantity_to_sell > 0) {
            throw new Exception("Stock insuficiente para el producto ID: $product_id. Se necesitan $quantity_to_sell más unidades.");
        }
    }

    // --- PASO C: Actualizar las finanzas (Caja) ---
    if ($data['payment_method'] === 'Caja') {
        $cash_stmt = $pdo->prepare(
            "INSERT INTO cash_box_transactions (type, amount, description) VALUES ('Venta', ?, ?)"
        );
        $cash_stmt->execute([
            $data['total_amount'], // Ingreso, por lo tanto positivo
            "Venta registrada con Recibo #" . $sale_id
        ]);
    }

    // --- PASO D: Si todo fue exitoso, confirmar la transacción ---
    $pdo->commit();

    // Enviar respuesta de éxito
    http_response_code(201); // Created
    echo json_encode([
        'message' => 'Venta registrada con éxito.',
        'sale_id' => $sale_id
    ]);

} catch (Exception $e) {
    // --- PASO E: Si algo falló, revertir TODOS los cambios ---
    $pdo->rollBack();

    // Enviar respuesta de error
    // Usamos 409 (Conflict) si el problema es de lógica de negocio como falta de stock
    http_response_code(409); 
    echo json_encode([
        'message' => 'No se pudo registrar la venta: ' . $e->getMessage()
    ]);
}