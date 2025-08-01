<?php
// --- LÓGICA DE LA PÁGINA ---

$page_title = 'Inventario';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    // Consulta actualizada para incluir el nombre de la categoría
    $sql = "
        SELECT
            p.id,
            p.name,
            p.sku,
            p.image_path,
            p.suggested_sale_price,
            c.name AS category_name, -- Obtenemos el nombre de la categoría
            COALESCE(SUM(poi.quantity_received - poi.quantity_sold), 0) AS stock_disponible,
            COALESCE(SUM(CASE WHEN po.status != 'Recibido' THEN poi.quantity - poi.quantity_received ELSE 0 END), 0) AS stock_en_espera
        FROM 
            products p
        LEFT JOIN 
            categories c ON p.category_id = c.id -- Unimos con la tabla de categorías
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
    $error_message = "Error al obtener los productos: " . $e->getMessage();
    $products = [];
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
                    <th>Categoría</th> <th>SKU</th>
                    <th>Stock Disponible</th>
                    <th>Stock en Espera</th>
                    <th>Precio Venta Sug.</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No hay productos en el inventario.</td>
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
                            <td>
                                <span class="badge"><?php echo htmlspecialchars($product['category_name'] ?? 'Sin categoría'); ?></span>
                            </td>
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
require_once 'includes/footer.php';
?>