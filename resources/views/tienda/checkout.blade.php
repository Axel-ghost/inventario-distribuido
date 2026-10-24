<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Confirmar compra</title>
    @vite(['resources/css/app.css'])
</head>

<body class="tienda-body">

    <x-tienda-nav />

    <main class="tienda-main">

        <h2 style="margin-bottom: 20px;">Confirmar compra</h2>

        @if ($errors->any())
            <div class="alert alert-error" style="background:#3a1414; border:1px solid #7a2626; color:#ff8080; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                @foreach ($errors->all() as $error)
                    <p style="margin:0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($carrito as $item)
                    <tr>
                        <td>{{ $item['nombre'] }}</td>
                        <td>{{ $item['cantidad'] }}</td>
                        <td>${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="venta-resumen" style="margin-top: 20px;">
            <div class="venta-total">
                <span>Total a pagar</span>
                <strong>${{ number_format($total, 2) }}</strong>
            </div>

            <form method="POST" action="/tienda/checkout">
                @csrf
                <x-order-button id="btnConfirmarCompra" label="Confirmar compra" />
            </form>
        </div>

    </main>

    <script>
        document.getElementById('btnConfirmarCompra').addEventListener('click', function () {
            const boton = this;
            playOrderAnimation(boton, {
                duration: 2000,
                onComplete: function () {
                    boton.closest('form').submit();
                }
            });
        });
    </script>

</body>

</html>