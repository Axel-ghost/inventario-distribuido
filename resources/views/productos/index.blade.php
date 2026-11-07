@extends('layouts.app')

@section('title', 'Productos')

@section('header_title', 'Productos')
@section('header_subtitle', 'Catálogo de productos registrados')

@section('content')

    <div class="table-actions-top">
        @if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
            <a href="/productos/crear" class="btn-process">
                <span class="btn-label">+ Nuevo producto</span>
            </a>
        @endif
    </div>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>IMAGEN</th>
                    <th>CÓDIGO</th>
                    <th>NOMBRE</th>
                    <th>CATEGORÍA</th>
                    <th>PROVEEDOR</th>
                    <th>PRECIO</th>
                    <th>EXISTENCIA</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>
                            @if ($producto->imagen_url)
                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" loading="lazy" class="table-img">
                            @else
                                <span style="color:var(--muted);font-size:12px;">Sin imagen</span>
                            @endif
                        </td>

                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria ?? 'Sin categoría' }}</td>
                        <td>{{ $producto->proveedor ?? 'Sin proveedor' }}</td>

                        <td>${{ number_format($producto->precio_venta, 2) }}</td>

                        <td>
                            {{ $producto->existencia }}
                            @if ($producto->existencia == 0)
                                <span class="status-badge status-danger">Agotado</span>
                            @elseif ($producto->existencia <= $producto->stock_minimo)
                                <span class="status-badge status-warning">Bajo</span>
                            @endif
                        </td>

                        <td>
                            <span class="status-badge {{ $producto->activo ? 'status-success' : 'status-secondary' }}">
                                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        <td>
                            <div class="actions-wrapper">
                                @if (Auth::user()->rol === 'administrador' || Auth::user()->rol === 'almacen')
                                    <a href="/productos/{{ $producto->id }}/editar" class="btn-action btn-edit" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </a>
                                @endif

                                @if (Auth::user()->rol === 'administrador')
                                    <form method="POST" action="/productos/{{ $producto->id }}/estado" style="margin: 0; display: flex;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action {{ $producto->activo ? 'btn-disable' : 'btn-enable' }}" title="{{ $producto->activo ? 'Desactivar' : 'Activar' }}">
                                            @if ($producto->activo)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:30px;color:var(--muted);">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection