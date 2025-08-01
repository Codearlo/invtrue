<?php
// --- LÓGICA DE PROCESAMIENTO DEL FORMULARIO ---

// Inicializar variables para mensajes
$success_message = '';
$error_message = '';

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    require_once 'config/database.php';

    // 1. Recoger datos del formulario
    $name = $_POST['name'] ?? '';
    $sku = $_POST['sku'] ?? null;
    $price = $_POST['suggested_sale_price'] ?? 0.00;
    $image_path = null;

    // 2. Validación básica
    if (empty($name) || empty($price)) {
        $error_message = 'El nombre y el precio son campos obligatorios.';
    } else {
        // 3. Manejo de la subida de la imagen
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/';
            // Crear el directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            // Generar un nombre de archivo único para evitar sobreescrituras
            $filename = uniqid() . '-' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $filename;

            // Mover el archivo subido al directorio de destino
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file; // Guardar la ruta en la variable
            } else {
                $error_message = 'Hubo un error al subir la imagen.';
            }
        }

        // 4. Insertar en la base de datos (si no hubo errores previos)
        if (empty($error_message)) {
            try {
                $sql = "INSERT INTO products (name, sku, image_path, suggested_sale_price) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $sku, $image_path, $price]);
                
                // Redirigir al inventario después de crear el producto para evitar reenvíos del formulario
                header("Location: inventario.php?status=success");
                exit();

            } catch (PDOException $e) {
                $error_message = 'Error al crear el producto: ' . $e->getMessage();
            }
        }
    }
}


// --- VISTA DE LA PÁGINA ---

$page_title = 'Crear Nuevo Producto';
// Opcional: podríamos crear un css 'formularios.css'
// $page_css = 'formularios.css'; 

require_once 'includes/header.php';
?>

<div class="card">
    <h3>Datos del Nuevo Producto</h3>

    <?php if (!empty($error_message)): ?>
        <div class="error-banner">
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>

    <form action="producto-nuevo.php" method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-group">
            <label for="name">Nombre del Producto</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="sku">SKU (Código)</label>
            <input type="text" id="sku" name="sku">
        </div>
        <div class="form-group">
            <label for="suggested_sale_price">Precio de Venta Sugerido (S/)</label>
            <input type="number" id="suggested_sale_price" name="suggested_sale_price" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="image">Imagen del Producto</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
            <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>