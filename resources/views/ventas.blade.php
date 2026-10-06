<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Ventas</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="ventas" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Ventas</h2>
                    <p>Historial de ventas registradas</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <a href="/ventas/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; margin-bottom: 20px;">
                <span class="btn-label">+ Nueva venta</span>
            </a>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Vendedor</th>
                        <th>Productos</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->usuario->name ?? '—' }}</td>
                            <td>
                                @if ($venta->pedido && $venta->pedido->detalles)
                                    @foreach ($venta->pedido->detalles as $detalle)
                                        {{ $detalle->producto->nombre ?? '—' }} (x{{ $detalle->cantidad }})@if (!$loop->last), @endif
                                    @endforeach
                                @else
                                    <span style="color: #888;">Sin detalles</span>
                                @endif
                            </td>
                            <td>${{ number_format($venta->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px; color: #888;">
                                No hay ventas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>