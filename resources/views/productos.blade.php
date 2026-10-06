<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild Gaming | Productos</title>
    @vite(['resources/css/app.css'])
</head>

<script>
    function toggleMenu(boton) {
        const dropdown = boton.nextElementSibling;
        const estaAbierto = dropdown.classList.contains('open');

        // Cerrar todos los menús abiertos
        document.querySelectorAll('.acciones-dropdown').forEach(d => d.classList.remove('open'));

        // Abrir/cerrar el actual
        if (!estaAbierto) {
            dropdown.classList.add('open');
        }
    }

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.acciones-menu')) {
            document.querySelectorAll('.acciones-dropdown').forEach(d => d.classList.remove('open'));
        }
    });
</script>

<body>

    <div class="dashboard">

        <x-sidebar active-page="productos" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Productos</h2>
                    <p>Catálogo de productos registrados</p>
                </div>
                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            @if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
                <a href="/productos/crear" class="btn-process"
                   style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; margin-bottom: 20px;">
                    <span class="btn-label">+ Nuevo producto</span>
                </a>
            @endif

            {{-- Layout de 2 columnas --}}
            <div class="productos-layout">

                {{-- PANEL IZQUIERDO: Categorías --}}
                <aside class="productos-sidebar">
                    <h3>Categorías</h3>

                    <button class="categoria-btn active" data-categoria="todas">
                        Todas
                    </button>

                    @foreach ($categorias as $cat)
                        <button class="categoria-btn" data-categoria="{{ $cat->id }}">
                            {{ $cat->nombre }}
                        </button>
                    @endforeach
                </aside>

                {{-- PANEL DERECHO: Productos --}}
                <div class="productos-contenido">

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Existencia</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="productos-body">
                            @forelse ($productos as $producto)
                                <tr data-categoria="{{ $producto->id_categoria }}">
                                    <td>{{ $producto->codigo }}</td>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="descripcion-cell" title="{{ $producto->descripcion }}">
                                        {{ $producto->descripcion }}
                                    </td>
                                    <td>${{ number_format($producto->precio_venta, 2) }}</td>
                                    <td>
                                        {{ $producto->existencia }}
                                        @if ($producto->existencia == 0)
                                            <span class="badge badge-danger">A</span>
                                        @elseif ($producto->existencia <= $producto->stock_minimo)
                                            <span class="badge badge-warning">B</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($producto->estado)
                                            <span class="badge badge-ok">Activo</span>
                                        @else
                                            <span class="badge badge-danger" style="background:#7f1d1d; color:#fecaca; padding:2px 8px; border-radius:12px; font-size:12px; font-weight:600;">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="acciones-menu">
                                            <button type="button" class="acciones-btn" onclick="toggleMenu(this)">
                                                ⋮
                                            </button>

                                            <div class="acciones-dropdown">
                                                @if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
                                                    <a href="/productos/{{ $producto->id }}/editar" class="accion-item">
                                                        ✎ Editar
                                                    </a>
                                                @endif

                                                @if (Auth::user()->rol === 'administrador')
                                                    <form method="POST" action="/productos/{{ $producto->id }}/estado">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="accion-item {{ $producto->estado ? 'accion-danger' : 'accion-ok' }}">
                                                            {{ $producto->estado ? '✕ Desactivar' : '✓ Activar' }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 20px; color: #888;">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Mensaje si el filtro no encuentra productos --}}
                    <p id="sin-productos" style="display: none; text-align: center; padding: 20px; color: #888;">
                        No hay productos en esta categoría.
                    </p>

                </div>

            </div>

        </main>
    </div>

    {{-- JavaScript para filtrar --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const botones = document.querySelectorAll('.categoria-btn');
            const filas = document.querySelectorAll('#productos-body tr[data-categoria]');
            const sinProductos = document.getElementById('sin-productos');

            botones.forEach(boton => {
                boton.addEventListener('click', function () {
                    const categoria = this.dataset.categoria;

                    // Actualizar botón activo
                    botones.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Filtrar filas
                    let visibles = 0;
                    filas.forEach(fila => {
                        if (categoria === 'todas' || fila.dataset.categoria === categoria) {
                            fila.style.display = '';
                            visibles++;
                        } else {
                            fila.style.display = 'none';
                        }
                    });

                    // Mostrar mensaje si no hay productos
                    sinProductos.style.display = visibles === 0 ? 'block' : 'none';
                });
            });
        });
    </script>

</body>
</html>