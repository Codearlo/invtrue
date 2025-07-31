<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
            $categories = $stmt->fetchAll();
            echo json_encode($categories);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener categorías: ' . $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'] ?? null;

        if (!$name) {
            http_response_code(400);
            echo json_encode(['message' => 'El nombre de la categoría es requerido.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            
            http_response_code(201); // 201 Created
            echo json_encode(['message' => 'Categoría creada con éxito', 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            // Código de error 23000 es para violación de restricción de integridad (ej: UNIQUE)
            if ($e->getCode() == 23000) {
                 http_response_code(409); // 409 Conflict
                 echo json_encode(['message' => 'Ya existe una categoría con ese nombre.']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al crear la categoría: ' . $e->getMessage()]);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}