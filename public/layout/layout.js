// public/layout/layout.js
document.addEventListener('DOMContentLoaded', async () => {
    // Carga el HTML del layout
    const response = await fetch('../layout/layout.html');
    const layoutHtml = await response.text();
    
    // Inserta el layout al inicio del <body>
    document.body.insertAdjacentHTML('afterbegin', layoutHtml);

    // Marca el enlace de la página actual como activo
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (currentPath === linkPath) {
            link.classList.add('active');
        }
    });
});