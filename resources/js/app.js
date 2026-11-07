
const syncThemeIcon = () => {
    const icon = document.getElementById('theme-icon');
    if (icon) {
        icon.textContent = document.documentElement.dataset.theme === 'dark' ? '☀︎' : '☁︎';
    }
};
 
document.addEventListener('DOMContentLoaded', syncThemeIcon);
 
// Delegado: sigue funcionando aunque el botón esté dentro del contenido reemplazado
document.addEventListener('click', (e) => {
    if (!e.target.closest('#theme-toggle')) return;
 
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    localStorage.setItem('theme', next);
    syncThemeIcon();
});
 
 
/* ==== SIDEBAR ACTIVO ==== */
function updateSidebarActive(url) {
    const path = new URL(url, location.origin).pathname;
 
    document.querySelectorAll('.nav-item').forEach((link) => {
        const href = new URL(link.href, location.origin).pathname;
        const active = href === '/'
            ? path === '/'
            : path === href || path.startsWith(href + '/');
 
        link.classList.toggle('active', active);
    });
}
 
 
/* ==== NAVEGACIÓN RÁPIDA (SPA) ==== */
let navController;
 
async function loadPage(url, { push = true } = {}) {
    // Cancela la petición anterior para que nunca gane una respuesta vieja
    navController?.abort();
    navController = new AbortController();
 
    try {
        const response = await fetch(url, { signal: navController.signal });
        if (!response.ok) throw new Error(response.status);
 
        const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
        const newContent = doc.querySelector('.dashboard-content');
        if (!newContent) throw new Error('La respuesta no trae .dashboard-content');
 
        // Estilos del <head> que la vista nueva necesite y aún no estén cargados
        doc.head.querySelectorAll('link[rel="stylesheet"]').forEach((l) => {
            const href = l.getAttribute('href');
            if (!document.head.querySelector(`link[href="${href}"]`)) {
                document.head.appendChild(l.cloneNode());
            }
        });
 
        // Los <script> traídos con DOMParser no se ejecutan: los recreamos
        newContent.querySelectorAll('script').forEach((old) => {
            const s = document.createElement('script');
            [...old.attributes].forEach((a) => s.setAttribute(a.name, a.value));
            s.textContent = old.textContent;
            old.replaceWith(s);
        });
 
        // Reemplazamos SOLO el contenido central
        document.querySelector('.dashboard-content').replaceWith(newContent);
        document.title = doc.title;
 
        updateSidebarActive(response.url);
        syncThemeIcon();
        window.scrollTo(0, 0);
 
        if (push) history.pushState({}, '', response.url);
    } catch (err) {
        if (err.name === 'AbortError') return;
        console.error('Error al navegar:', err);
        push ? (window.location.href = url) : window.location.reload();
    }
}
 
document.addEventListener('click', (e) => {
    // Respeta Ctrl/Cmd/Shift+clic, clic central, etc.
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
 
    const link = e.target.closest('a[href]');
 
    // Ignorar enlaces externos, anclas, descargas o que abran en otra pestaña
    if (!link || link.host !== location.host || link.target === '_blank' || link.hasAttribute('download')) return;
    if (link.getAttribute('href').startsWith('#')) return;
 
    // Ignorar logout y login
    if (link.pathname.startsWith('/logout') || link.pathname.startsWith('/login')) return;
 
    e.preventDefault();
    loadPage(link.href);
});
 
// Botón "Atrás" / "Adelante" del navegador
window.addEventListener('popstate', () => loadPage(location.href, { push: false }));
 