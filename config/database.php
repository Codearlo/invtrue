<?php
$host = 'localhost';
$dbname = 'u347334547_invtrue';
$username = 'u347334547_user_inv';
$password = 'CH7322a#';
$charset = 'utf8';

// Opciones de configuración para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    // Crear la instancia de PDO
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un error y detiene la ejecución
    // En un entorno de producción, deberías registrar este error en lugar de mostrarlo
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}