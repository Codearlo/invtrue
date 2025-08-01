<?php
/**
 * Función para el control de caché de archivos CSS y JS.
 */
function version_file($file) {
    if (!file_exists($file)) {
        return $file;
    }
    $version = filemtime($file);
    return $file . '?v=' . $version;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'GestorPro'; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="<?php echo version_file('css/base.css'); ?>">
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo version_file('css/' . htmlspecialchars($page_css)); ?>">
    <?php endif; ?>
</head>
<body>
<div class="app-container">
    <nav class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo-link">
                <span class="sidebar-logo">G</span>
                <h1 class="sidebar-title">GestorPro</h1>
            </a>
        </div>
        <ul class="nav-links">
            <li class="nav-item">
                <a href="dashboard.php"><i class='bx bxs-dashboard'></i></a>
                <span class="tooltip">Dashboard</span>
            </li>
            <li class="nav-item">
                <a href="inventario.php"><i class='bx bxs-package'></i></a>
                <span class="tooltip">Inventario</span>
            </li>
            <li class="nav-item">
                <a href="compras.php"><i class='bx bxs-cart'></i></a>
                <span class="tooltip">Compras</span>
            </li>
            <li class="nav-item">
                <a href="ordenes.php"><i class='bx bxs-receipt'></i></a>
                <span class="tooltip">Órdenes</span>
            </li>
            <li class="nav-item">
                <a href="pos.php"><i class='bx bxs-store-alt'></i></a>
                <span class="tooltip">Punto de Venta</span>
            </li>
            <li class="nav-item">
                <a href="finanzas.php"><i class='bx bxs-bank'></i></a>
                <span class="tooltip">Finanzas</span>
            </li>
            <li class="nav-item">
                <a href="reportes.php"><i class='bx bxs-bar-chart-alt-2'></i></a>
                <span class="tooltip">Reportes</span>
            </li>
        </ul>
    </nav>

    <div class="main-content">
        <header class="main-header">
            <h2><?php echo $page_title ?? 'Bienvenido'; ?></h2>
        </header>
        <main>