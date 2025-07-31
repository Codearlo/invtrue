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
            $stmt = $pdo->query("SELECT * FROM settings");
            $settings_raw = $stmt->fetchAll();
            // Convertir el array de arrays en un objeto clave-valor para fácil acceso en JS
            $settings = [];
            foreach ($settings_raw as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            echo json_encode($settings);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al obtener la configuración: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['message' => 'No se proporcionaron datos para actualizar.']);
            exit;
        }

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            // Iterar sobre los datos enviados y actualizar cada clave
            foreach ($data as $key => $value) {
                $stmt->execute([$value, $key]);
            }
            $pdo->commit();
            http_response_code(200);
            echo json_encode(['message' => 'Configuración actualizada correctamente.']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la configuración: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
        break;
}