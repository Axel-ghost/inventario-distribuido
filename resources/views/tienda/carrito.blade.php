<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Carrito</title>
    @vite(['resources/css/app.css'])
</head>

<body class="tienda-body">

    <x-tienda-nav />

    <main class="tienda-main">

        <h2 style="margin-bottom: 20px;">Tu carrito</h2>

        @if (session('status'))
            <p class="tienda-status">{{ session('status') }}</p>
        @endif

        @if (empty($carrito))
            <p style="color: var(--muted);">Tu carrito está vacío. <a href="/tienda" style="color: var(--accent);">Ir al catálogo</a></p>
        @else
            <table class="data-table">
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
                            <td>{{ $item['nombre'] }}</td>
                            <td>${{ number_format($item['precio'], 2) }}</td>
                            <td>
                                <form method="POST" action="/tienda/carrito/actualizar" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $item['producto_id'] }}">
                                    <input
                                        type="number"
                                        name="cantidad"
                                        min="1"
                                        value="{{ $item['cantidad'] }}"
                                        class="venta-cantidad-input"
                                        onchange="this.form.submit()"
                                    >
                                </form>
                            </td>
                            <td>${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
                            <td>
                                <form method="POST" action="/tienda/carrito/quitar">
                                    @csrf
                                    <input type="hidden" name="producto_id" value="{{ $item['producto_id'] }}">
                                    <button type="submit" class="venta-quitar">✕</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="venta-resumen" style="margin-top: 20px;">
                <div class="venta-total">
                    <span>Total</span>
                    <strong>${{ number_format($total, 2) }}</strong>
                </div>

                <a href="/tienda/checkout" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 220px;">
                    <span class="btn-label">Continuar compra</span>
                </a>
            </div>
        @endif

    </main>

</body>

</html>