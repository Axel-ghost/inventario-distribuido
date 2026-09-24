<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CoreBuild Gaming | Productos</title>

    @vite(['resources/css/app.css'])
</head>

<body>
    
    <div class="dashboard">

    <x-sidebar active-page="productos" />

        <!-- CONTENIDO -->
        <main class="dashboard-content">

            <header class="dashboard-header">
                <h2>Productos</h2>
                <p>Catálogo de productos registrados</p>
            </header>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Existencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria }}</td>
                        <td>${{ number_format($producto->precio, 2) }}</td>
                        <td>
                            {{ $producto->existencia }}
                            @if ($producto->existencia == 0)
                                <span class="badge badge-danger">Agotado</span>
                            @elseif ($producto->existencia <= $producto->stock_minimo)
                                <span class="badge badge-warning">Bajo</span>
                            @endif
                        </td>
                        <td>{{ $producto->activo ? 'Activo' : 'Inactivo' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>