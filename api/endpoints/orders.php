<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$pdo = require '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        try {
            // Obtener todas las órdenes, ordenadas por la más reciente primero
            $stmt = $pdo->query("SELECT * FROM purchase_orders ORDER BY order_date DESC");
            $orders = $stmt->fetchAll();
            echo json_encode($orders);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener las órdenes: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Obtener ID de la orden desde el parámetro en la URL (ej: /api/orders?id=123)
        $order_id = $_GET['id'] ?? null;
        if (!$order_id) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de la orden no proporcionado.']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $new_status = $data['status'] ?? null;

        if (!$new_status) {
            http_response_code(400);
            echo json_encode(['message' => 'Nuevo estado no proporcionado.']);
            exit;
        }

        $pdo->beginTransaction();
        try {
            // Si el nuevo estado es "Recibido", actualiza el inventario físico
            if ($new_status === 'Recibido') {
                // Actualiza la cantidad recibida en los ítems para que coincida con la cantidad pedida
                $update_items_stmt = $pdo->prepare(
                    "UPDATE purchase_order_items SET quantity_received = quantity WHERE purchase_order_id = ?"
                );
                $update_items_stmt->execute([$order_id]);
            }

            // Actualiza el estado de la orden
            $update_order_stmt = $pdo->prepare(
                "UPDATE purchase_orders SET status = ? WHERE id = ?"
            );
            $update_order_stmt->execute([$new_status, $order_id]);

            $pdo->commit();
            http_response_code(200);
            echo json_encode(['message' => 'Orden actualizada correctamente.']);

        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la orden: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}