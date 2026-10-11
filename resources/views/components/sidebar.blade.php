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
        @endif

        @if (in_array($rol, ['administrador', 'almacen']))
            <a href="/pedidos" class="nav-item {{ $activePage === 'pedidos' ? 'active' : '' }}">
                <span>📋</span>
                Pedidos
                @if ($rol === 'administrador')
                    @php
                        $pendientes = \App\Models\Pedido::whereIn('estado', ['solicitado', 'en_revision'])->count();
                    @endphp
                    @if ($pendientes > 0)
                        <span class="sidebar-badge">{{ $pendientes }}</span>
                    @endif
                @endif
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