// public/modules/layout/layout.js
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // La ruta del fetch es correcta, asumiendo que el script se carga desde
        // una página en /public/modules/nombre-modulo/
        const response = await fetch('../../layout/layout.html');
        
        if (!response.ok) {
            throw new Error(`No se pudo cargar el layout. Estado: ${response.status}`);
        }
        
        const layoutHtml = await response.text();
        // Usamos 'afterbegin' para insertar el layout al inicio del body
        document.body.insertAdjacentHTML('afterbegin', layoutHtml);

        // --- LÓGICA MEJORADA PARA EL ENLACE ACTIVO ---
        const currentPath = window.location.pathname; // ej: /public/modules/dashboard/dashboard.html
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname; // ej: /public/modules/inventory/inventory.html
            
            // Comparamos si la ruta actual coincide con la del enlace.
            // Esto es más preciso que solo comparar el final del string.
            if (currentPath === linkPath) {
                link.classList.add('active');
            }
        });
    } catch (error) {
        console.error("Error crítico al inicializar el layout:", error);
        document.body.innerHTML = `<div style="padding: 2rem; color: red; background: #fff;"><b>Error:</b> No se pudo cargar la barra de navegación. Revisa la consola (F12) para más detalles.</div>` + document.body.innerHTML;
    }
});