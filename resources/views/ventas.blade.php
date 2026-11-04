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
                    <p class="page-subtitle">Historial de ventas registradas</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px; font-weight: 500;">{{ session('status') }}</p>
            @endif

            <div class="table-actions-top">
                @if (in_array(Auth::user()->rol, ['administrador', 'vendedor']))
                    <a href="/ventas/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; height: 44px; border-radius: 8px;">
                        <span class="btn-label">+ Nueva venta</span>
                    </a>
                @endif
            </div>

            <!-- Contenedor de la Tabla -->
            <div class="table-container">
                <table class="data-table-clean">
                    <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>FECHA</th>
                            <th>VENDEDOR</th>
                            <th>PRODUCTOS</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr onclick="window.location='/ventas/{{ $venta->id }}'" style="cursor: pointer;">
                                <td>{{ $venta->folio }}</td>
                                <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                                <td>{{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</td>
                                <td>
                                    @foreach ($venta->detalles as $detalle)
                                        {{ $detalle->producto->nombre }} (x{{ $detalle->cantidad }}){{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </td>
                                <td style="font-weight: 600;">${{ number_format($venta->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--muted);">
                                    No hay ventas registradas todavía
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</body>

</html>