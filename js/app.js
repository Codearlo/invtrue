import * as api from './apiService.js';
import * as ui from './ui.js';

const mainContent = document.getElementById('app-content');

// --- Router Sencillo ---
async function router() {
    const hash = window.location.hash || '#/dashboard';
    mainContent.innerHTML = '<h2>Cargando...</h2>';

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
                    api.getCategories() // Asumiendo que tendrás este endpoint
                ]);
                mainContent.innerHTML = ui.renderNewPurchaseForm(productsForPurchase, categories);
                break;
            
            case '#/ordenes':
                const orders = await api.getOrders();
                mainContent.innerHTML = ui.renderOrdersView(orders);
                break;
            
            // Agrega más rutas aquí (finanzas, reportes, etc.)

            default:
                mainContent.innerHTML = '<h2>Página no encontrada</h2>';
        }
    } catch (error) {
        console.error('Error en el router:', error);
        mainContent.innerHTML = `<p class="error">Error al cargar la vista: ${error.message}</p>`;
    }

    updateActiveNavLink();
}

function updateActiveNavLink() {
    const hash = window.location.hash || '#/dashboard';
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === hash) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// --- Manejadores de Eventos Globales (Delegación) ---
document.addEventListener('submit', async (event) => {
    if (event.target.id === 'new-product-form') {
        event.preventDefault();
        const formData = new FormData(event.target);
        try {
            await api.createProduct(formData);
            alert('Producto creado con éxito');
            window.location.hash = '#/inventario';
        } catch (error) {
            console.error('Error al crear producto:', error);
            alert('Error al crear el producto.');
        }
    }
    
    if (event.target.id === 'new-purchase-form') {
         event.preventDefault();
         // Lógica para procesar el formulario de compra
         alert('Lógica de compra no implementada en este ejemplo.');
    }
});


// --- Inicialización ---
window.addEventListener('hashchange', router);
window.addEventListener('DOMContentLoaded', router);