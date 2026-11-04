<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild_{{ $venta->folio }}</title>
    
    @vite(['resources/css/app.css', 'resources/css/dark.css', 'resources/js/app.js'])

    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="ventas" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>{{ $venta->folio }}</h2>
                    <p>Registrada por {{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <div class="ticket" id="ticket">

                <div class="ticket-header">
                    <h3>CoreBuild Gaming</h3>
                    <p>Comprobante de venta</p>
                </div>

                <div class="ticket-meta">
                    <div>
                        <span>Folio</span>
                        <strong>{{ $venta->folio }}</strong>
                    </div>
                    <div>
                        <span>Fecha</span>
                        <strong>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>
                        <span>Vendedor</span>
                        <strong>{{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</strong>
                    </div>
                </div>

                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="ticket-total">
                    <span>Total</span>
                    <strong>${{ number_format($venta->total, 2) }}</strong>
                </div>

            </div>

            <div class="ticket-actions no-print">
                <button type="button" class="btn-process" onclick="window.print()" style="max-width: 220px;">
                    <span class="btn-label">Imprimir / Guardar PDF</span>
                </button>

                <a href="/ventas" class="venta-quitar" style="margin-left: 16px; text-decoration: none;">Volver al historial</a>
            </div>

        </main>

    </div>

</body>

</html>