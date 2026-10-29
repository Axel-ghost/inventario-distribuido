<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Catálogo</title>
    @vite(['resources/css/app.css'])
</head>

<body class="tienda-body">

    <x-tienda-nav />

    <main class="tienda-main">

        @if (session('status'))
            <p class="tienda-status">{{ session('status') }}</p>
        @endif

        <form method="GET" action="/tienda" class="tienda-buscador">
            <input type="text" name="q" placeholder="Busca por nombre o categoría..." value="{{ request('q') }}">
            <button type="submit" class="btn-process" style="max-width: 140px;">
                <span class="btn-label">Buscar</span>
            </button>
        </form>

        <div class="tienda-grid">
            @forelse ($productos as $producto)
                <div class="producto-card">

                    <div class="producto-card-imagen">
                        <img src="{{ $producto->imagen_url ?: 'https://picsum.photos/seed/' . $producto->id . '/400/300' }}" alt="{{ $producto->nombre }}">
                    </div>

                    <div class="producto-card-body">
                        @if ($producto->categoria)
                            <span class="badge badge-ok">{{ $producto->categoria }}</span>
                        @endif

                        <h3>{{ $producto->nombre }}</h3>
                        <p>{{ $producto->descripcion }}</p>

                        <div class="producto-card-footer">
                            <strong>${{ number_format($producto->precio_venta, 2) }}</strong>

                            <form method="POST" action="/tienda/carrito/agregar">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <button type="submit" class="btn-process" style="max-width: 160px;">
                                    <span class="btn-label">Agregar</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <p style="color: var(--muted);">No encontramos productos con esa búsqueda.</p>
            @endforelse
        </div>

    </main>

</body>

</html>