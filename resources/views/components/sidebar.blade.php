<div>
    <!-- Simplicity is an acquired taste. - Katharine Gerould -->
     <aside class="sidebar">

    <div class="sidebar-brand">
        <span class="brand-line"></span>
        <h1>CoreBuild</h1>
        <span class="brand-subtitle">GAMING</span>
    </div>

    <nav class="sidebar-nav">

        <p class="nav-title">PRINCIPAL</p>

        <a href="/dashboard" class="nav-item {{ $activePage === 'dashboard' ? 'active' : '' }}">
            <span>◈</span>
            Dashboard
        </a>

        <a href="/productos" class="nav-item {{ $activePage === 'productos' ? 'active' : '' }}">
            <span>▣</span>
            Productos
        </a>

        <a href="/inventario" class="nav-item {{ $activePage === 'inventario' ? 'active' : '' }}">
            <span>▤</span>
            Inventario
        </a>

        <p class="nav-title">OPERACIONES</p>

        <a href="/ventas" class="nav-item {{ $activePage === 'ventas' ? 'active' : '' }}">
            <span>▰</span>
            Ventas
        </a>

    </nav>

    <div class="sidebar-footer">
        <p style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">
            {{ Auth::user()->name }} {{ Auth::user()->apellidos }}
        </p>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="nav-item" style="width: 100%; background: none; border: none; cursor: pointer; text-align: left;">
                <span>⏻</span>
                Cerrar sesión
            </button>
        </form>
    </div>

</aside>
</div>