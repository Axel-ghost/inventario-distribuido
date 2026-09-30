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
                <div>
                    <h2>Productos</h2>
                    <p>Catálogo de productos registrados</p>
                </div>
                <x-profile-card />
            </header>

            <table class="data-table">
                @if (session('status'))
    <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
@endif

@if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
    <a href="/productos/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; margin-bottom: 20px;">
        <span class="btn-label">+ Nuevo producto</span>
    </a>
@endif

<thead>
    <tr>
        <th>Código</th>
        <th>Nombre</th>
        <th>Categoría</th>
        <th>Precio</th>
        <th>Existencia</th>
        <th>Estado</th>
        <th></th>
    </tr>
</thead>
<tbody>
    @foreach ($productos as $producto)
        <tr>
            <td>{{ $producto->codigo }}</td>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->categoria }}</td>
            <td>${{ number_format($producto->precio_venta, 2) }}</td>
            <td>
                {{ $producto->existencia }}
                @if ($producto->existencia == 0)
                    <span class="badge badge-danger">Agotado</span>
                @elseif ($producto->existencia <= $producto->stock_minimo)
                    <span class="badge badge-warning">Bajo</span>
                @endif
            </td>
            <td>{{ $producto->activo ? 'Activo' : 'Inactivo' }}</td>
            <td>
                @if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
                    <a href="/productos/{{ $producto->id }}/editar" class="venta-quitar" style="color: var(--accent); text-decoration: none; margin-right: 12px;">
                        Editar
                    </a>
                @endif

                @if (Auth::user()->rol === 'administrador')
                    <form method="POST" action="/productos/{{ $producto->id }}/estado" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="venta-quitar" style="color: var(--accent);">
                            {{ $producto->activo ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
</tbody>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>