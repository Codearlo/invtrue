<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'u347334547_invtrue';
$username = 'u347334547_user_inv';
$password = 'CH7322a#';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    return $pdo;
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}