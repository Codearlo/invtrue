<?php
header('Content-Type: application/json');
$pdo = require '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validación básica
    if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
        http_response_code(400);
        echo json_encode(['message' => 'Datos de compra inválidos.']);
        exit;
    }

    $pdo->beginTransaction();

    try {
        // 1. Crear la orden de compra
        $stmt = $pdo->prepare(
            "INSERT INTO purchase_orders (supplier, order_date, payment_method, total_cost, status) 
             VALUES (?, CURDATE(), ?, ?, 'Pedido')"
        );
        $stmt->execute([$data['supplier'], $data['payment_method'], $data['total_cost']]);
        $purchase_order_id = $pdo->lastInsertId();

        // 2. Insertar los ítems de la orden y generar lotes
        $prefix_stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'store_prefix'");
        $prefix = $prefix_stmt->fetchColumn() ?: 'PROD';

        $item_stmt = $pdo->prepare(
            "INSERT INTO purchase_order_items (purchase_order_id, product_id, lot_code, quantity, purchase_price_per_unit) 
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($data['items'] as $item) {
            // Generar código de lote único
            $lot_code = $prefix . '-P' . $item['product_id'] . '-' . date('dmyHis') . rand(10, 99);
            $item_stmt->execute([
                $purchase_order_id,
                $item['product_id'],
                $lot_code,
                $item['quantity'],
                $item['purchase_price_per_unit']
            ]);
        }

        // 3. Afectar finanzas (Caja o Tarjeta)
        if ($data['payment_method'] === 'Caja') {
            $cash_stmt = $pdo->prepare(
                "INSERT INTO cash_box_transactions (type, amount, description) 
                 VALUES ('Compra', ?, ?)"
            );
            // El monto de compra es un egreso, por lo tanto negativo
            $cash_stmt->execute([-$data['total_cost'], 'Compra de mercadería, orden #' . $purchase_order_id]);
        }
        // Aquí iría la lógica para las tarjetas de crédito si se implementa un registro de transacciones por tarjeta.

        $pdo->commit();
        http_response_code(201);
        echo json_encode(['message' => 'Compra registrada con éxito', 'purchase_order_id' => $purchase_order_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => 'Error al procesar la compra: ' . $e->getMessage()]);
    }

} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}