// public/modules/dashboard/dashboard.js
import { supabase } from '../../../supabase-client.js';

const loadMetrics = async () => {
    // Cargar total de productos
    const { count: productCount, error: productError } = await supabase
        .from('products')
        .select('*', { count: 'exact', head: true });
    if (!productError) {
        document.getElementById('total-products').textContent = productCount;
    }

    // Cargar total de clientes
    const { count: clientCount, error: clientError } = await supabase
        .from('clients')
        .select('*', { count: 'exact', head: true });
    if (!clientError) {
        document.getElementById('total-clients').textContent = clientCount;
    }
};

const loadLowStockAlerts = async () => {
    const LOW_STOCK_THRESHOLD = 5;
    const { data, error } = await supabase
        .from('products')
        .select('name, stock_quantity')
        .lt('stock_quantity', LOW_STOCK_THRESHOLD)
        .order('stock_quantity', { ascending: true });
    
    const alertContainer = document.getElementById('low-stock-alert');
    if (error) {
        alertContainer.innerHTML = `<p class="text-red-500">No se pudieron cargar las alertas.</p>`;
        return;
    }

    if (data.length === 0) {
        alertContainer.innerHTML = `<p class="text-green-500">¡Todo bien! No hay productos con stock bajo.</p>`;
    } else {
        alertContainer.innerHTML = `
            <ul class="list-disc list-inside space-y-2">
                ${data.map(p => `<li><strong>${p.name}:</strong> ${p.stock_quantity} unidades restantes</li>`).join('')}
            </ul>
        `;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    loadMetrics();
    loadLowStockAlerts();
});