<?php
/**
 * Función para el control de caché de archivos CSS y JS.
 * Añade la fecha de modificación del archivo a la URL para forzar al navegador
 * a descargar la nueva versión si ha cambiado.
 *
 * @param string $file La ruta al archivo desde la raíz del proyecto.
 * @return string La ruta del archivo con el parámetro de versión.
 */
function version_file($file) {
    if (!file_exists($file)) {
        return $file; // Devuelve la ruta original si el archivo no existe
    }
    // Añade el timestamp de la última modificación como parámetro 'v'
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

    <link rel="stylesheet" href="<?php echo version_file('css/base.css'); ?>">
    
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?php echo version_file('css/' . htmlspecialchars($page_css)); ?>">
    <?php endif; ?>
</head>
<body>
<div class="app-container">
    <nav class="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-logo">G</span>
            <h1 class="sidebar-title">GestorPro</h1>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="compras.php">Compras</a></li>
            <li><a href="ordenes.php">Órdenes</a></li>
            <li><a href="pos.php">Punto de Venta</a></li>
            <li><a href="finanzas.php">Finanzas</a></li>
            <li><a href="reportes.php">Reportes</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <header class="main-header">
            <h2><?php echo $page_title ?? 'Bienvenido'; ?></h2>
            </header>
        <main>