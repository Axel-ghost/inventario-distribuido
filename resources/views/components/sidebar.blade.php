@php
    $rol = Auth::user()->rol;
@endphp

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

        @if (in_array($rol, ['administrador', 'vendedor', 'almacen']))
            <a href="/productos" class="nav-item {{ $activePage === 'productos' ? 'active' : '' }}">
                <span>▣</span>
                Productos
            </a>

            <a href="/inventario" class="nav-item {{ $activePage === 'inventario' ? 'active' : '' }}">
                <span>▤</span>
                Inventario
            </a>

            <a href="/requisicion" class="nav-item {{ $activePage === 'requisicion' ? 'active' : '' }}">
                <span>▱</span>
                Requisición 
            </a>
        @endif

        @if (in_array($rol, ['administrador', 'vendedor', 'caja']))
            <p class="nav-title">OPERACIONES</p>

            <a href="/ventas" class="nav-item {{ $activePage === 'ventas' ? 'active' : '' }}">
                <span>▰</span>
                Ventas
            </a>
        @endif

        @if ($rol === 'administrador')
            <p class="nav-title">ADMINISTRACIÓN</p>

            <a href="/usuarios" class="nav-item {{ $activePage === 'usuarios' ? 'active' : '' }}">
                <span>◉</span>
                Usuarios
            </a>
        @endif

    </nav>

</aside>