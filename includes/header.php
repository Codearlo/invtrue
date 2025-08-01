<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'GestorPro'; ?></title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="assets/icons/favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <h1>GestorPro</h1>
    </header>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="inventario.php">Inventario</a>
        <a href="compras.php">Compras</a>
        <a href="ordenes.php">Órdenes</a>
        <a href="pos.php">Punto de Venta</a>
        <a href="finanzas.php">Finanzas</a>
        <a href="reportes.php">Reportes</a>
    </nav>

    <main id="app-content">