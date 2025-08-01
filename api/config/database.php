<?php
// Muestra todos los errores de PHP (útil para depuración, desactiva en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Reemplaza con tus credenciales de la base de datos de Hostinger
$host = 'localhost'; // ej: 'localhost' o un servidor remoto
$dbname = 'u347334547_invtrue';
$username = 'u347334547_user_inv';
$password = 'CH7322a#';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    return $pdo; // Devuelve la conexión para que otros scripts la puedan usar
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    // No devuelvas el objeto PDO si falla la conexión
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}