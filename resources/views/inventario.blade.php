@extends('layouts.app')

@section('title', 'Inventario')

@section('header_title', 'Inventario')
@section('header_subtitle', 'Existencia actual y alertas de stock')

@section('content')

    <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
        
        <div class="stat-card">
            <div class="stat-info">
                <span>Productos agotados</span>
                <strong style="color: #dc2626;">{{ $agotados }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Stock bajo</span>
                <strong style="color: #d97706;">{{ $bajos }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Total de productos</span>
                <strong>{{ $productos->count() }}</strong>
            </div>
        </div>

    </div>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>CÓDIGO</th>
                    <th>NOMBRE</th>
                    <th>CATEGORÍA</th>
                    <th>EXISTENCIA</th>
                    <th>STOCK MÍNIMO</th>
                    <th>ESTADO</th>
                    
                    @if (Auth::user()->rol === 'almacen')
                        <th>ACCIONES</th>
                    @endif
                    
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria ?? 'Sin categoría' }}</td>
                        <td>{{ $producto->existencia }}</td>
                        <td>{{ $producto->stock_minimo }}</td>
                        
                        <td>
                            @if ($producto->existencia == 0)
                                <span class="status-badge status-danger">Agotado</span>
                            @elseif ($producto->existencia <= $producto->stock_minimo)
                                <span class="status-badge status-warning">Bajo</span>
                            @else
                                <span class="status-badge status-success">Óptimo</span>
                            @endif
                        </td>

                        @if (Auth::user()->rol === 'almacen')
                            <td>
                                @if ($producto->existencia <= $producto->stock_minimo)
                                    <!-- Botón de acción con el mismo estilo de iconos -->
                                    <a href="/pedidos/crear?producto_id={{ $producto->id }}" class="btn-action btn-reabasto" title="Solicitar reabasto">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </a>
                                @endif
                            </td>
                        @endif
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->rol === 'almacen' ? 7 : 6 }}" style="text-align: center; padding: 30px; color: var(--muted);">
                            No hay productos en el inventario.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection