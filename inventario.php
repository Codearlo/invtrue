<?php
// --- LÓGICA DE LA PÁGINA ---

// 1. Definir variables de la página
$page_title = 'Inventario';
// Opcional: si creamos estilos específicos para la tabla de inventario
// $page_css = 'inventario.css'; 

// 2. Incluir archivos necesarios
require_once 'config/database.php'; // Conexión a la base de datos
require_once 'includes/header.php'; // Cabecera y menú

// 3. Obtener los productos de la base de datos
try {
    // Consulta que calcula el stock disponible y en espera para cada producto
    $sql = "
        SELECT
            p.id,
            p.name,
            p.sku,
            p.image_path,
            p.suggested_sale_price,
            COALESCE(SUM(poi.quantity_received - poi.quantity_sold), 0) AS stock_disponible,
            COALESCE(SUM(CASE WHEN po.status != 'Recibido' THEN poi.quantity - poi.quantity_received ELSE 0 END), 0) AS stock_en_espera
        FROM 
            products p
        LEFT JOIN 
            purchase_order_items poi ON p.id = poi.product_id
        LEFT JOIN 
            purchase_orders po ON poi.purchase_order_id = po.id
        GROUP BY 
            p.id
        ORDER BY
            p.name ASC
    ";
    
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    // Manejo de errores de base de datos
    $error_message = "Error al obtener los productos: " . $e->getMessage();
    $products = []; // Asegurarse de que $products es un array vacío en caso de error
}

?>

<div class="card">
    <div class="card-header">
        <h3>Listado de Productos</h3>
        <a href="producto-nuevo.php" class="btn btn-primary">Crear Nuevo Producto</a>
    </div>
    
    <?php if (isset($error_message)): ?>
        <div class="error-banner">
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Stock Disponible</th>
                    <th>Stock en Espera</th>
                    <th>Precio Venta Sug.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No hay productos en el inventario.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if (!empty($product['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></td>
                            <td><?php echo (int)$product['stock_disponible']; ?></td>
                            <td><?php echo (int)$product['stock_en_espera']; ?></td>
                            <td>S/ <?php echo number_format($product['suggested_sale_price'], 2); ?></td>
                            <td>
                                <a href="#" class="btn btn-secondary">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php
// Incluimos el pie de página
require_once 'includes/footer.php';
?>