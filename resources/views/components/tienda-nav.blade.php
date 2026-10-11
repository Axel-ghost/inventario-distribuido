<header class="tienda-nav">
    <a href="/tienda" class="tienda-logo">CoreBuild <span>Gaming</span></a>

    <nav class="tienda-nav-links">
        <a href="/tienda">Catálogo</a>

        <a href="/tienda/carrito" class="tienda-carrito-link">
            🛒 Carrito
            @php $cantidadCarrito = collect(session('carrito', []))->sum('cantidad'); @endphp
            @if ($cantidadCarrito > 0)
                <span class="tienda-carrito-badge">{{ $cantidadCarrito }}</span>
            @endif
        </a>

        @auth
            <span class="tienda-nav-user">Hola, {{ Auth::user()->name }}</span>
            <form method="POST" action="/logout" style="display: inline;">
                @csrf
                <button type="submit" class="tienda-nav-logout">Cerrar sesión</button>
            </form>
        @else
            <a href="/">Iniciar sesión</a>
        @endauth
    </nav>
</header>