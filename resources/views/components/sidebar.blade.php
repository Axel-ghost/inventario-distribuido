@php
    $rol = Auth::user()->rol;
@endphp

<aside class="sidebar">

    <div class="sidebar-brand" style="width: 100%; padding: 0 15px; box-sizing: border-box; text-align: center; margin-bottom: 30px;">
        <!-- Se agregó max-width: 100% y object-fit para que se adapte al ancho del sidebar -->
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width: 100%; height: auto; max-height: 55px; object-fit: contain; display: block; margin: 0 auto 10px auto;">
        <span class="brand-subtitle" style="font-size: 9px; letter-spacing: 4px; color: #94a3b8; display: block;">SISTEMA DE GESTIÓN</span>
    </div>

    <p class="nav-title" style="margin: 0 0 14px; text-align: center;">AGENCIA: {{ mb_strtoupper(Auth::user()->agencia?->nombre ?? 'Todas') }}</p>

    <nav class="sidebar-nav">

        @if ($rol !== 'contabilidad')
            <p class="nav-title">PRINCIPAL</p>

            <a href="/dashboard" class="nav-item {{ $activePage === 'dashboard' ? 'active' : '' }}">
                <span>◈</span>
                Dashboard
            </a>
        @endif

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
            <a href="/inventario/carga" class="nav-item {{ $activePage === 'carga' ? 'active' : '' }}">
                <span>▥</span>
                Carga masiva
            </a>

            <a href="/pedidos" class="nav-item {{ $activePage === 'pedidos' ? 'active' : '' }}">
                <span>▱</span>
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

        @if (in_array($rol, ['administrador', 'vendedor', 'caja', 'contabilidad']))
            <p class="nav-title">OPERACIONES</p>

            <a href="/ventas" class="nav-item {{ $activePage === 'ventas' ? 'active' : '' }}">
                <span>▰</span>
                Ventas
            </a>
        @endif

        @if (in_array($rol, ['administrador', 'contabilidad']))
            <p class="nav-title">FINANZAS</p>

            <a href="/contabilidad" class="nav-item {{ $activePage === 'contabilidad' ? 'active' : '' }}">
                <span>◐</span>
                Contabilidad
            </a>
        @endif

        @if ($rol === 'administrador')
            <p class="nav-title">ADMINISTRACIÓN</p>

            <a href="/usuarios" class="nav-item {{ $activePage === 'usuarios' ? 'active' : '' }}">
                <span>◉</span>
                Usuarios
            </a>

            @if (Auth::user()->agencia_id === null)
                <a href="/agencias" class="nav-item {{ $activePage === 'agencias' ? 'active' : '' }}">
                    <span>◫</span>
                    Agencias
                </a>
            @endif
        @endif

    </nav>

    <div class="sidebar-footer" style="margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1); display: flex; justify-content: center;">
        <button id="theme-toggle" class="action-btn" type="button" title="Cambiar tema" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; background: transparent;border: 1px solid rgba(255, 255, 255, 0.2); color: #cbd5e1; border-radius: 50%; cursor: pointer; transition: 0.2s;">
            <span id="theme-icon" style="font-size: 18px;">☁︎</span>
        </button>
    </div>

</aside>