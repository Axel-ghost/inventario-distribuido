<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Inventario</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="inventario" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Inventario</h2>
                    <p>Existencia actual y alertas de stock</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <div class="dashboard-grid" style="margin-bottom: 24px;">

                <div class="stat-card">
                    <span>Productos agotados</span>
                    <h3 style="color: #ef4444;">{{ $agotados }}</h3>
                </div>

                <div class="stat-card">
                    <span>Stock bajo</span>
                    <h3 style="color: #f59e0b;">{{ $bajos }}</h3>
                </div>

                <div class="stat-card">
                    <span>Total de productos</span>
                    <h3>{{ $productos->count() }}</h3>
                </div>

            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Existencia</th>
                        <th>Stock mínimo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                        <tr>
                            <td>{{ $producto->codigo }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->categoria }}</td>
                            <td>{{ $producto->existencia }}</td>
                            <td>{{ $producto->stock_minimo }}</td>
                            <td>
                                @if ($producto->existencia == 0)
                                    <span class="badge badge-danger">Agotado</span>
                                    @if (Auth::user()->rol === 'almacen')
                                        <a href="/pedidos/crear?producto_id={{ $producto->id }}" style="color: var(--accent); font-size: 12px; display: block; margin-top: 4px;">Solicitar reabasto</a>
                                    @endif
                                @elseif ($producto->existencia <= $producto->stock_minimo)
                                    <span class="badge badge-warning">Bajo</span>
                                    @if (Auth::user()->rol === 'almacen')
                                        <a href="/pedidos/crear?producto_id={{ $producto->id }}" style="color: var(--accent); font-size: 12px; display: block; margin-top: 4px;">Solicitar reabasto</a>
                                    @endif
                                @else
                                    <span class="badge badge-ok">Óptimo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>