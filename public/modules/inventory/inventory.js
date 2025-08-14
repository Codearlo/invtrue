// public/modules/inventory/inventory.js
import { supabase } from '../../../supabase-client.js';

// --- DOM Elements ---
const addProductBtn = document.getElementById('add-product-btn');
const productFormContainer = document.getElementById('product-form-container');
const productForm = document.getElementById('product-form');
const cancelBtn = document.getElementById('cancel-btn');
const productsTableBody = document.getElementById('products-table-body');
const formTitle = document.getElementById('form-title');
const productIdInput = document.getElementById('product-id');

// --- Functions ---
const showForm = (show = true, product = null) => {
    productForm.reset();
    productIdInput.value = '';
    productFormContainer.classList.toggle('hidden', !show);

    if (product) {
        formTitle.textContent = 'Editar Producto';
        productIdInput.value = product.id;
        document.getElementById('name').value = product.name;
        document.getElementById('sku').value = product.sku;
        document.getElementById('description').value = product.description;
        document.getElementById('purchase_price').value = product.purchase_price;
        document.getElementById('sale_price').value = product.sale_price;
        document.getElementById('stock_quantity').value = product.stock_quantity;
    } else {
        formTitle.textContent = 'Agregar Nuevo Producto';
    }
};

const loadProducts = async () => {
    const { data: products, error } = await supabase.from('products').select('*').order('created_at', { ascending: false });
    if (error) {
        console.error('Error cargando productos:', error);
        return;
    }
    productsTableBody.innerHTML = products.map(product => `
        <tr class="border-b border-slate-200">
            <td class="p-3">${product.name}</td>
            <td class="p-3">S/ ${Number(product.sale_price).toFixed(2)}</td>
            <td class="p-3">${product.stock_quantity}</td>
            <td class="p-3 space-x-2">
                <button class="edit-btn text-blue-600" data-id="${product.id}">Editar</button>
                <button class="delete-btn text-red-600" data-id="${product.id}">Borrar</button>
            </td>
        </tr>
    `).join('');
};

const handleFormSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(productForm);
    const productData = Object.fromEntries(formData.entries());
    const id = productIdInput.value;

    let error;
    if (id) {
        ({ error } = await supabase.from('products').update(productData).eq('id', id));
    } else {
        ({ error } = await supabase.from('products').insert([productData]));
    }

    if (error) {
        console.error('Error guardando producto:', error);
        alert('Hubo un error al guardar el producto.');
    } else {
        showForm(false);
        loadProducts();
    }
};

const handleDelete = async (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        const { error } = await supabase.from('products').delete().eq('id', id);
        if (error) console.error('Error eliminando producto:', error);
        else loadProducts();
    }
};

const handleEdit = async (id) => {
    const { data: product, error } = await supabase.from('products').select('*').eq('id', id).single();
    if (error) console.error('Error obteniendo producto:', error);
    else showForm(true, product);
};

// --- Event Listeners ---
addProductBtn.addEventListener('click', () => showForm());
cancelBtn.addEventListener('click', () => showForm(false));
productForm.addEventListener('submit', handleFormSubmit);
productsTableBody.addEventListener('click', (e) => {
    if (e.target.classList.contains('delete-btn')) {
        handleDelete(e.target.dataset.id);
    } else if (e.target.classList.contains('edit-btn')) {
        handleEdit(e.target.dataset.id);
    }
});

// --- Initial Load ---
loadProducts();