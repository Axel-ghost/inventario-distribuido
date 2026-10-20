<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Compra confirmada</title>
    @vite(['resources/css/app.css'])
</head>

<body class="tienda-body">

    <x-tienda-nav />

    <main class="tienda-main">

        <div class="ticket">

            <div class="ticket-header">
                <h3>¡Gracias por tu compra!</h3>
                <p>Folio {{ $pedido['folio'] }}</p>
            </div>

            <div class="ticket-meta">
                <div>
                    <span>Fecha</span>
                    <strong>{{ $pedido['fecha'] }}</strong>
                </div>
                <div>
                    <span>Cliente</span>
                    <strong>{{ $pedido['cliente'] }}</strong>
                </div>
            </div>

            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedido['items'] as $item)
                        <tr>
                            <td>{{ $item['nombre'] }}</td>
                            <td>{{ $item['cantidad'] }}</td>
                            <td>${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="ticket-total">
                <span>Total</span>
                <strong>${{ number_format($pedido['total'], 2) }}</strong>
            </div>

        </div>

        <a href="/tienda" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px;">
            <span class="btn-label">Seguir comprando</span>
        </a>

    </main>

</body>

</html>