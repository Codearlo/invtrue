const BASE_URL = '/api'; // Apunta a la carpeta /api/

async function fetchAPI(endpoint, options = {}) {
    const url = `${BASE_URL}${endpoint}`;
    
    // Configuración por defecto para las cabeceras
    const defaultHeaders = {
        'Accept': 'application/json'
    };

    // No establecer 'Content-Type' para FormData, el navegador lo hace.
    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
    }
    
    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers,
        },
    };

    try {
        const response = await fetch(url, config);
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Error HTTP ${response.status}`);
        }
        return response.json();
    } catch (error) {
        console.error(`Error en la llamada API a ${endpoint}:`, error);
        throw error;
    }
}

// --- Endpoints ---

// Productos
export const getProducts = () => fetchAPI('/products');
export const createProduct = (formData) => fetchAPI('/products', {
    method: 'POST',
    body: formData, // Se envía como FormData, no JSON
});

// Órdenes
export const getOrders = () => fetchAPI('/orders');
export const updateOrder = (orderId, data) => fetchAPI(`/orders?id=${orderId}`, {
    method: 'PUT',
    body: JSON.stringify(data),
});

// Compras
export const createPurchase = (purchaseData) => fetchAPI('/purchases', {
    method: 'POST',
    body: JSON.stringify(purchaseData),
});

// Categorías
export const getCategories = () => fetchAPI('/categories');