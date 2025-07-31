// --- Funciones de Renderizado de Vistas Completas ---

export function renderDashboard() {
    return `
        <div class="card">
            <h2>Dashboard</h2>
            <p>Bienvenido a GestorPro. Selecciona una opción del menú para comenzar.</p>
        </div>
    `;
}

export function renderInventoryView(products) {
    if (!products || products.length === 0) {
        return `
            <div class="card">
                <h2>Inventario</h2>
                <p>No hay productos en el inventario.</p>
                <a href="#/inventario/nuevo" class="btn btn-primary">Crear Nuevo Producto</a>
            </div>
        `;
    }

    const productRows = products.map(createProductTableRow).join('');

    return `
        <h2>Inventario</h2>
        <a href="#/inventario/nuevo" class="btn btn-primary" style="margin-bottom: 1rem;">Crear Nuevo Producto</a>
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
                    </tr>
                </thead>
                <tbody>
                    ${productRows}
                </tbody>
            </table>
        </div>
    `;
}

export function renderOrdersView(orders) {
     if (!orders || orders.length === 0) {
        return `<h2>Órdenes de Compra</h2><p>No hay órdenes registradas.</p>`;
    }
    const orderRows = orders.map(createOrderTableRow).join('');
    return `
        <h2>Órdenes de Compra</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proveedor</th>
                        <th>Fecha de Orden</th>
                        <th>Costo Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    ${orderRows}
                </tbody>
            </table>
        </div>
    `;
}


// --- Formularios ---

export function renderNewProductForm() {
    return `
        <h2>Nuevo Producto</h2>
        <form id="new-product-form" class="card">
            <div class="form-group">
                <label for="name">Nombre del Producto</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="sku">SKU (Opcional)</label>
                <input type="text" id="sku" name="sku">
            </div>
            <div class="form-group">
                <label for="image">Imagen del Producto</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
             <div class="form-group">
                <label for="suggested_sale_price">Precio de Venta Sugerido</label>
                <input type="number" id="suggested_sale_price" name="suggested_sale_price" step="0.01" min="0">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </form>
    `;
}


export function renderNewPurchaseForm(products, categories) {
    // Esta es una versión simplificada. Un formulario real necesitaría JS para agregar/quitar ítems dinámicamente.
    const productOptions = products.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    
    return `
        <h2>Registrar Nueva Compra</h2>
        <form id="new-purchase-form" class="card">
            <div class="form-group">
                <label for="supplier">Proveedor</label>
                <input type="text" id="supplier" name="supplier">
            </div>
            
            <h3>Items</h3>
            <div id="purchase-items-container">
                <div class="purchase-item">
                    <select name="product_id">
                        <option value="">Seleccionar producto...</option>
                        ${productOptions}
                    </select>
                    <input type="number" name="quantity" placeholder="Cantidad" min="1">
                    <input type="number" name="purchase_price_per_unit" placeholder="Precio unitario" step="0.01" min="0">
                </div>
            </div>
            <button type="button" id="add-item-btn" class="btn btn-secondary">Añadir Otro Item</button>
            <hr>
            
            <div class="form-group">
                <label for="payment_method">Método de Pago</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="Caja">Caja (Efectivo)</option>
                    </select>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Compra</button>
        </form>
    `;
}


// --- Funciones de Creación de Componentes de UI (Filas de tabla, etc.) ---

function createProductTableRow(product) {
    const image = product.image_path ? `<img src="${product.image_path}" alt="${product.name}" width="50">` : 'N/A';
    return `
        <tr>
            <td>${image}</td>
            <td>${product.name}</td>
            <td>${product.sku || 'N/A'}</td>
            <td>${product.stock_disponible || 0}</td>
            <td>${product.stock_en_espera || 0}</td>
            <td>S/ ${parseFloat(product.suggested_sale_price).toFixed(2)}</td>
        </tr>
    `;
}

function createOrderTableRow(order) {
    return `
        <tr>
            <td>${order.id}</td>
            <td>${order.supplier || 'N/A'}</td>
            <td>${order.order_date}</td>
            <td>S/ ${parseFloat(order.total_cost).toFixed(2)}</td>
            <td>${order.status}</td>
            <td>
                <button class="btn btn-secondary" data-order-id="${order.id}">Ver</button>
            </td>
        </tr>
    `;
}