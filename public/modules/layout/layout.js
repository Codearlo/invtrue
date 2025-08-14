// public/layout/layout.js
document.addEventListener('DOMContentLoaded', async () => {
    try {
        // --- CORRECCIÓN AQUÍ ---
        // La ruta ahora es relativa a la ubicación del archivo HTML (ej. dashboard.html),
        // no a la ubicación del propio archivo .js.
        // Desde 'public/modules/dashboard/', necesitamos subir dos niveles para llegar a 'public/'.
        const response = await fetch('../../layout/layout.html');
        
        if (!response.ok) {
            throw new Error(`No se pudo cargar el layout. Estado: ${response.status}`);
        }
        
        const layoutHtml = await response.text();
        document.body.insertAdjacentHTML('afterbegin', layoutHtml);

        // Marca el enlace de la página actual como activo
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            // Compara las rutas de forma más flexible para que funcione correctamente
            const linkPath = new URL(link.href).pathname;
            if (currentPath.endsWith(linkPath.substring(linkPath.lastIndexOf('/')))) {
                link.classList.add('active');
            }
        });
    } catch (error) {
        console.error("Error crítico al inicializar el layout:", error);
        // Muestra un error visible para el usuario si el layout no carga
        document.body.innerHTML = `<div style="padding: 2rem; color: red;"><b>Error:</b> No se pudo cargar la barra de navegación. Revisa la consola (F12) para más detalles.</div>` + document.body.innerHTML;
    }
});
