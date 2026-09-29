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
                    @foreach ($ventas as $venta)
                        <tr>
                            <td>{{ $venta['fecha_venta'] }}</td>
                            <td>{{ $venta['usuario'] }}</td>
                            <td>
                                @foreach ($venta['items'] as $item)
                                    {{ $item['producto'] }} (x{{ $item['cantidad'] }})@if (!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>${{ number_format($venta['total'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>