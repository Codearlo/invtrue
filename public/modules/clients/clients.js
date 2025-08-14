// public/modules/clients/clients.js
import { supabase } from '../../../supabase-client.js';

const addClientBtn = document.getElementById('add-client-btn');
const clientFormContainer = document.getElementById('client-form-container');
const clientForm = document.getElementById('client-form');
const cancelBtn = document.getElementById('cancel-btn');
const clientsTableBody = document.getElementById('clients-table-body');
const formTitle = document.getElementById('form-title');
const clientIdInput = document.getElementById('client-id');

const showForm = (show = true, client = null) => {
    clientForm.reset();
    clientIdInput.value = '';
    clientFormContainer.classList.toggle('hidden', !show);

    if (client) {
        formTitle.textContent = 'Editar Cliente';
        clientIdInput.value = client.id;
        document.getElementById('name').value = client.name;
        document.getElementById('email').value = client.email;
        document.getElementById('phone').value = client.phone;
        document.getElementById('address').value = client.address;
    } else {
        formTitle.textContent = 'Agregar Nuevo Cliente';
    }
};

const loadClients = async () => {
    const { data, error } = await supabase.from('clients').select('*').order('name', { ascending: true });
    if (error) {
        console.error('Error cargando clientes:', error);
        return;
    }
    clientsTableBody.innerHTML = data.map(c => `
        <tr class="border-b border-slate-200 hover:bg-slate-50">
            <td class="p-3 font-medium">${c.name}</td>
            <td class="p-3">${c.email || ''}</td>
            <td class="p-3">${c.phone || ''}</td>
            <td class="p-3 space-x-2">
                <button class="edit-btn text-blue-600 hover:underline" data-id="${c.id}">Editar</button>
                <button class="delete-btn text-red-600 hover:underline" data-id="${c.id}">Borrar</button>
            </td>
        </tr>
    `).join('');
    if (data.length === 0) {
        clientsTableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-slate-500">No hay clientes registrados.</td></tr>`;
    }
};

const handleFormSubmit = async (e) => {
    e.preventDefault();
    const clientData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value,
    };
    const id = clientIdInput.value;

    let error;
    if (id) {
        ({ error } = await supabase.from('clients').update(clientData).eq('id', id));
    } else {
        ({ error } = await supabase.from('clients').insert([clientData]));
    }

    if (error) {
        alert(`Error al guardar: ${error.message}`);
    } else {
        showForm(false);
        loadClients();
    }
};

const handleDelete = async (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
        const { error } = await supabase.from('clients').delete().eq('id', id);
        if (error) alert(`Error al eliminar: ${error.message}`);
        else loadClients();
    }
};

const handleEdit = async (id) => {
    const { data, error } = await supabase.from('clients').select('*').eq('id', id).single();
    if (error) alert(`Error al cargar cliente: ${error.message}`);
    else showForm(true, data);
};

addClientBtn.addEventListener('click', () => showForm());
cancelBtn.addEventListener('click', () => showForm(false));
clientForm.addEventListener('submit', handleFormSubmit);
clientsTableBody.addEventListener('click', (e) => {
    const { classList, dataset } = e.target;
    if (classList.contains('delete-btn')) handleDelete(dataset.id);
    if (classList.contains('edit-btn')) handleEdit(dataset.id);
});

loadClients();