document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');
    const icon = document.getElementById('theme-icon');

    // Sincronizar el icono al cargar la página
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    if (icon) icon.textContent = current === 'dark' ? '☀' : '☁︎';

    // Evento de clic para cambiar de tema
    toggle?.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        if (icon) icon.textContent = newTheme === 'dark' ? '☀' : '☁︎';
    });
});
