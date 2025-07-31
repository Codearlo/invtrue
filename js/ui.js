// --- Vistas Completas ---

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
    const productRows = products.map(p => `
        <tr>
            <td>${p.image_path ? `<img src="${p.image_path}" alt="${p.name}" width="50">` : 'N/A'}</td>
            <td>${p.name}</td>
            <td>${p.sku || 'N/A'}</td>
            <td>${p.stock_disponible || 0}</td>
            <td>${p.stock_en_espera || 0}</td>
            <td>S/ ${parseFloat(p.suggested_sale_price).toFixed(2)}</td>
        </tr>
    `).join('');
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
                <tbody>${productRows}</tbody>
            </table>
        </div>
    `;
}

export function renderOrdersView(orders) { /* ... (código existente sin cambios) ... */ }

// --- Vista del Punto de Venta (POS) ---
export function renderPOSView(products) {
    return `
        <h2>Punto de Venta</h2>
        <div class="pos-container">
            <div id="product-catalog" class="pos-catalog">
                ${createProductCatalog(products)}
            </div>
            <div id="cart-container" class="pos-cart">
                ${createCartView({})}
            </div>
        </div>
    `;
}

// --- Componentes del POS ---

function createProductCatalog(products) {
    const availableProducts = products.filter(p => p.stock_disponible > 0);
    if (availableProducts.length === 0) {
        return '<p>No hay productos con stock disponible para la venta.</p>';
    }
    return availableProducts.map(createPOSProductCard).join('');
}

function createPOSProductCard(product) {
    const image = product.image_path ? `<img src="${product.image_path}" alt="${product.name}">` : '<div class="no-image">Sin Imagen</div>';
    return `
        <div class="pos-product-card">
            ${image}
            <div class="pos-product-info">
                <h4>${product.name}</h4>
                <p>Stock: ${product.stock_disponible}</p>
                <p class="price">S/ ${parseFloat(product.suggested_sale_price).toFixed(2)}</p>
            </div>
            <button class="btn btn-primary add-to-cart-btn" 
                data-product-id="${product.id}"
                data-name="${product.name}"
                data-price="${product.suggested_sale_price}"
                data-stock="${product.stock_disponible}">
                Añadir
            </button>
        </div>
    `;
}

export function createCartView(cart) {
    const cartItems = Object.values(cart);
    const total = cartItems.reduce((sum, item) => sum + (item.quantity * item.price), 0);
    const cartIsEmpty = cartItems.length === 0;

    const itemsHTML = cartItems.map(item => `
        <li class="cart-item">
            <span class="item-name">${item.name}</span>
            <div class="item-controls">
                <button class="btn-control decrement-btn" data-product-id="${item.productId}">-</button>
                <span class="item-quantity">${item.quantity}</span>
                <button class="btn-control increment-btn" data-product-id="${item.productId}" data-stock="${item.stock}">+</button>
            </div>
            <span class="item-subtotal">S/ ${(item.quantity * item.price).toFixed(2)}</span>
        </li>
    `).join('');

    return `
        <h3>Carrito de Compra</h3>
        ${cartIsEmpty ? '<p id="empty-cart-message">El carrito está vacío.</p>' : ''}
        <ul id="cart-items-list">${itemsHTML}</ul>
        <div class="cart-total">
            <strong>Total:</strong>
            <strong id="cart-total-amount">S/ ${total.toFixed(2)}</strong>
        </div>
        <button id="finalize-sale-btn" class="btn btn-primary" ${cartIsEmpty ? 'disabled' : ''}>
            Finalizar Venta
        </button>
    `;
}


// --- Formularios ---
export function renderNewProductForm() { /* ... (código existente) ... */ }
export function renderNewPurchaseForm(products, categories) { /* ... (código existente) ... */ }