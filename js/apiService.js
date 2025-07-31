const BASE_URL = '/api'; // Apunta a la carpeta /api/

async function fetchAPI(endpoint, options = {}) {
    const url = `${BASE_URL}${endpoint}`;
    
    const defaultHeaders = {
        'Accept': 'application/json'
    };

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
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return response.json();
        }
        return {};

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
    body: formData,
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

// Ventas
export const createSale = (saleData) => fetchAPI('/sales', {
    method: 'POST',
    body: JSON.stringify(saleData),
});