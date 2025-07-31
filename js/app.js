import * as api from './apiService.js';
import * as ui from './ui.js';

const mainContent = document.getElementById('app-content');
// Estado del Carrito de Compras
let currentCart = {}; 

// --- Router Sencillo ---
async function router() {
    const hash = window.location.hash || '#/dashboard';
    mainContent.innerHTML = '<h2>Cargando...</h2>';
    currentCart = {}; // Limpiar el carrito al cambiar de vista
    
    updateActiveNavLink();

    try {
        switch (hash) {
            case '#/dashboard':
                mainContent.innerHTML = ui.renderDashboard();
                break;

            case '#/inventario':
                const products = await api.getProducts();
                mainContent.innerHTML = ui.renderInventoryView(products);
                break;
            
            case '#/inventario/nuevo':
                 mainContent.innerHTML = ui.renderNewProductForm();
                 break;

            case '#/compras':
                const [productsForPurchase, categories] = await Promise.all([
                    api.getProducts(),
                    api.getCategories()
                ]);
                mainContent.innerHTML = ui.renderNewPurchaseForm(productsForPurchase, categories);
                break;
            
            case '#/ordenes':
                const orders = await api.getOrders();
                mainContent.innerHTML = ui.renderOrdersView(orders);
                break;
            
            case '#/pos':
                const productsWithStock = await api.getProducts();
                mainContent.innerHTML = ui.renderPOSView(productsWithStock);
                break;

            default:
                mainContent.innerHTML = '<h2>Página no encontrada</h2>';
        }
    } catch (error) {
        console.error('Error en el router:', error);
        mainContent.innerHTML = `<p class="error">Error al cargar la vista: ${error.message}</p>`;
    }
}

function updateActiveNavLink() {
    const hash = window.location.hash || '#/dashboard';
    document.querySelectorAll('.nav-link').forEach(link => {
        link.getAttribute('href') === hash ? link.classList.add('active') : link.classList.remove('active');
    });
}

// --- Lógica del Carrito de Compras ---

function addToCart(productId, name, price, stock) {
    if (currentCart[productId] && currentCart[productId].quantity >= stock) {
        alert('No se puede añadir más de lo que hay en stock.');
        return;
    }

    if (currentCart[productId]) {
        currentCart[productId].quantity++;
    } else {
        currentCart[productId] = {
            productId: productId, name: name,
            price: parseFloat(price), quantity: 1, stock: parseInt(stock)
        };
    }
    updateCartView();
}

function incrementCartItem(productId) {
    const item = currentCart[productId];
    if (item && item.quantity < item.stock) {
        item.quantity++;
        updateCartView();
    }
}

function decrementCartItem(productId) {
    const item = currentCart[productId];
    if (item) {
        item.quantity--;
        if (item.quantity <= 0) {
            delete currentCart[productId];
        }
        updateCartView();
    }
}

function updateCartView() {
    const cartContainer = document.getElementById('cart-container');
    if (cartContainer) {
        cartContainer.innerHTML = ui.createCartView(currentCart);
    }
}

async function finalizeSale() {
    const finalizeBtn = document.getElementById('finalize-sale-btn');
    finalizeBtn.disabled = true;
    finalizeBtn.textContent = 'Procesando...';

    const saleData = {
        total_amount: Object.values(currentCart).reduce((sum, item) => sum + (item.quantity * item.price), 0),
        payment_method: 'Caja',
        items: Object.values(currentCart).map(item => ({
            product_id: item.productId, quantity: item.quantity, sale_price_per_unit: item.price
        }))
    };
    
    try {
        const result = await api.createSale(saleData);
        alert(`Venta #${result.sale_id} registrada con éxito.`);
        await router(); // Recargar la vista del POS para actualizar stock
    } catch (error) {
        alert(`Error al registrar la venta: ${error.message}`);
        finalizeBtn.disabled = false;
        finalizeBtn.textContent = 'Finalizar Venta';
    }
}

// --- Manejador de Eventos Global (Delegación) ---
document.addEventListener('click', (event) => {
    if (event.target.matches('.add-to-cart-btn')) {
        const productData = event.target.dataset;
        addToCart(productData.productId, productData.name, productData.price, productData.stock);
    }
    if (event.target.matches('.increment-btn')) {
        incrementCartItem(event.target.dataset.productId);
    }
    if (event.target.matches('.decrement-btn')) {
        decrementCartItem(event.target.dataset.productId);
    }
    if (event.target.id === 'finalize-sale-btn') {
        finalizeSale();
    }
});

document.addEventListener('submit', async (event) => {
    if (event.target.id === 'new-product-form') { /* ... (código existente) ... */ }
});

// --- Inicialización ---
window.addEventListener('hashchange', router);
window.addEventListener('DOMContentLoaded', router);