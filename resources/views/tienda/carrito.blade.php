<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAJAJ | Carrito</title>
    @vite(['resources/css/tienda.css'])
</head>

<body class="tienda-body">

    <!-- Navbar idéntico al de la tienda, pero con fondo transparente -->
    <header class="tienda-navbar" style="background: transparent !important; border-bottom: none !important; backdrop-filter: none !important; box-shadow: none !important;">
        <div class="nav-container">
            
            <!-- Logo -->
            <a href="/tienda" class="nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="BAJAJ" style="max-height: 45px; object-fit: contain;">
            </a>

            <!-- Enlaces centrales -->
            <div class="nav-links">
                <div class="nav-dropdown">
                    <button class="nav-dropdown-btn" type="button">MOTOS ▾</button>
                    <div class="nav-dropdown-content">
                        <!-- Aquí no hay carrusel, así que mandamos al catálogo con el ancla -->
                        <a href="/tienda#vista-pasarela">Boxer</a>
                        <a href="/tienda#vista-pasarela">Dominar</a>
                        <a href="/tienda#vista-pasarela">Platina</a>
                        <a href="/tienda#vista-pasarela">Pulsar</a>
                        <a href="/tienda#vista-pasarela">Avenger</a>
                    </div>
                </div>
                <a href="/tienda#promos">PROMOS</a>
                <a href="/tienda#financiamiento">FINANCIAMIENTO</a>
                <a href="/tienda#servicio">SERVICIO</a>
            </div>

            <!-- Acciones a la derecha -->
            <div class="nav-actions">
                @auth
                    <form method="POST" action="/logout" style="margin: 0;">
                        @csrf
                        <button type="submit" class="nav-logout" title="Cerrar Sesión" style="color: #fff; background: none; border: none; cursor: pointer; font-size: 14px; font-weight: 600;">
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a href="/" style="color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; text-transform: uppercase;">Iniciar sesión</a>
                @endauth
            </div>
            
        </div>
    </header>

    <!-- Contenido del Carrito -->
    <main class="tienda-main" style="padding-top: 60px;">
        <div class="carrito-wrapper">
            <h2 class="carrito-titulo">Tu Carrito de Compras</h2>

            @if (session('status'))
                <div class="tienda-status" style="margin-bottom: 20px;">{{ session('status') }}</div>
            @endif

            <div class="carrito-card">
                @if (empty($carrito))
                    <div class="carrito-vacio">
                        <h3>Tu carrito está vacío 🛒</h3>
                        <p>Aún no has agregado ninguna motocicleta.</p>
                        <a href="/tienda" class="btn-volver-catalogo">← Ir al catálogo</a>
                    </div>
                @else
                    <table class="carrito-tabla">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carrito as $item)
                                <tr>
                                    <td>
                                        <div class="carrito-producto">
                                            <img src="{{ $item['imagen_url'] ?? 'https://picsum.photos/seed/' . $item['producto_id'] . '/100/100' }}" alt="{{ $item['nombre'] }}">
                                            <div class="carrito-producto-info">
                                                <h4>{{ $item['nombre'] }}</h4>
                                                <span>Código: {{ $item['codigo'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-weight: 500;">${{ number_format($item['precio'], 2) }}</td>
                                    <td>
                                        <form method="POST" action="/tienda/carrito/actualizar" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="producto_id" value="{{ $item['producto_id'] }}">
                                            <input type="number" name="cantidad" min="1" value="{{ $item['cantidad'] }}" class="carrito-cantidad" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td style="font-weight: 700;">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
                                    <td>
                                        <form method="POST" action="/tienda/carrito/quitar" style="margin: 0;">
                                            @csrf
                                            <input type="hidden" name="producto_id" value="{{ $item['producto_id'] }}">
                                            <button type="submit" class="btn-eliminar-item" title="Eliminar producto">✕</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="carrito-footer">
                        <div class="carrito-total">
                            <span>Total a pagar</span>
                            <strong>${{ number_format($total, 2) }}</strong>
                        </div>
                        <a href="/tienda/checkout" class="nav-cart" style="text-decoration: none; padding: 12px 30px; font-size: 16px; border-radius: 8px; border: 1px solid #fff; font-weight: bold;">
                            Continuar compra &rarr;
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Script exclusivo para que el menú desplegable funcione con clic en esta vista -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.nav-dropdown');
        const dropdownBtn = document.querySelector('.nav-dropdown-btn');

        if (dropdown && dropdownBtn) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    dropdown.classList.remove('open');
                }
            });
        }
    });
    </script>

</body>
</html>