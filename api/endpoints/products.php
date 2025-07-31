<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Para desarrollo. Considera restringirlo en producción.
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$pdo = require '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

// Manejar solicitud pre-vuelo OPTIONS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

switch ($method) {
    case 'GET':
        try {
            // Consulta que calcula stock disponible y en espera
            $sql = "
                SELECT
                    p.*,
                    COALESCE(SUM(poi.quantity_received - poi.quantity_sold), 0) AS stock_disponible,
                    COALESCE(SUM(CASE WHEN po.status != 'Recibido' THEN poi.quantity - poi.quantity_received ELSE 0 END), 0) AS stock_en_espera
                FROM products p
                LEFT JOIN purchase_order_items poi ON p.id = poi.product_id
                LEFT JOIN purchase_orders po ON poi.purchase_order_id = po.id
                GROUP BY p.id
            ";
            $stmt = $pdo->query($sql);
            $products = $stmt->fetchAll();
            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener productos: ' . $e->getMessage()]);
        }
        break;

    case 'POST':
        try {
            // Manejo de datos del formulario y subida de imagen
            $name = $_POST['name'] ?? null;
            $sku = $_POST['sku'] ?? null;
            $price = $_POST['suggested_sale_price'] ?? 0.00;
            $image_path = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $filename = uniqid() . '-' . basename($_FILES['image']['name']);
                $target_file = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_path = 'uploads/' . $filename;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO products (name, sku, image_path, suggested_sale_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $sku, $image_path, $price]);
            
            http_response_code(201);
            echo json_encode(['message' => 'Producto creado con éxito', 'id' => $pdo->lastInsertId()]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}