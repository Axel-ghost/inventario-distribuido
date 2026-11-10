const syncThemeIcon = () => {
    const icon = document.getElementById('theme-icon');
    if (icon) {
        icon.textContent = document.documentElement.dataset.theme === 'dark' ? '☀︎' : '☁︎';
    }
};
 
document.addEventListener('DOMContentLoaded', syncThemeIcon);
 
document.addEventListener('click', (e) => {
    if (!e.target.closest('#theme-toggle')) return;
    const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    localStorage.setItem('theme', next);
    syncThemeIcon();
});
 
function updateSidebarActive(url) {
    const path = new URL(url, location.origin).pathname;
 
    document.querySelectorAll('.nav-item').forEach((link) => {
        const href = new URL(link.href, location.origin).pathname;
        const active = href === '/' ? path === '/' : path === href || path.startsWith(href + '/');
        link.classList.toggle('active', active);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateSidebarActive(window.location.href);
});
 
let navController;
 
async function loadPage(url, { push = true } = {}) {
    navController?.abort();
    navController = new AbortController();
 
    try {
        const response = await fetch(url, { signal: navController.signal });
        if (!response.ok) throw new Error(response.status);
 
        const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
        const newContent = doc.querySelector('.dashboard-content');
        if (!newContent) throw new Error('La respuesta no trae .dashboard-content');
 
        // Reemplazamos el contenido
        document.querySelector('.dashboard-content').replaceWith(newContent);
        document.title = doc.title;
 
        // Ejecutamos los scripts
        newContent.querySelectorAll('script').forEach((old) => {
            const s = document.createElement('script');
            [...old.attributes].forEach((a) => s.setAttribute(a.name, a.value));
            s.textContent = old.textContent;
            old.replaceWith(s);
        });
 
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
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    const link = e.target.closest('a[href]');
    if (!link || link.host !== location.host || link.target === '_blank' || link.hasAttribute('download')) return;
    if (link.getAttribute('href').startsWith('#')) return;
    if (link.pathname.startsWith('/logout') || link.pathname.startsWith('/login')) return;
    e.preventDefault();
    loadPage(link.href);
});
 
window.addEventListener('popstate', () => loadPage(location.href, { push: false }));