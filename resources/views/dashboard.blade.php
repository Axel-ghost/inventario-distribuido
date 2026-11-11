@extends('layouts.app')
 
@section('title', 'Dashboard')
 
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Panel de control')
 
@section('content')
 
    <section class="welcome-section">
        <div>
            <p class="welcome-label">
                CONCESIONARIO BAJAJ
            </p>
            <h3>
                Bienvenido de nuevo
            </h3>
 
            <p>
                Aquí tienes un resumen del estado actual
                de tu inventario y operaciones.
            </p>
        </div>
    </section>
 
    <section class="stats-grid">
 
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">
                ◈
            </div>
            <div>
                <span>Productos</span>
                <strong>{{ $totalProductos }}</strong>
            </div>
        </article>
 
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">
                ▤
            </div>
            <div>
                <span>Stock bajo</span>
                <strong>{{ $stockBajo }}</strong>
            </div>
        </article>
 
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">
                ▰
            </div>
            <div>
                <span>Ventas del mes</span>
                <strong>${{ number_format($ventasDelMes, 2) }}</strong>
            </div>
        </article>
 
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">
                ▱
            </div>
            <div>
                <span>Pedidos pendientes</span>
                <strong>{{ $pedidosPendientes }}</strong>
            </div>
        </article>
 
    </section>
 
    <section class="dashboard-grid">
 
        <article class="panel">
 
            <div class="panel-header">
                <div>
                    <span>INVENTARIO</span>
                    <h3>
                        Stock bajo
                    </h3>
                </div>
 
                <a href="/inventario" class="panel-link">Ver todo</a>
            </div>
 
            <div class="inventory-list">
 
                @forelse ($productosStockBajo as $producto)
                    <div class="inventory-item">
 
                        <div>
                            <strong>
                                {{ $producto->nombre }}
                            </strong>
 
                            <span>
                                {{ $producto->categoria ?? 'Sin categoría' }}
                            </span>
                        </div>
 
                        <strong class="stock-warning">
                            {{ $producto->existencia }} unidades
                        </strong>
 
                    </div>
                @empty
                    <p>No hay productos con stock bajo por el momento.</p>
                @endforelse
 
            </div>
 
        </article>
 
        <article class="panel">
 
            <div class="panel-header">
                <div>
                    <span>ACTIVIDAD</span>
                    <h3>
                        Operaciones recientes
                    </h3>
                </div>
                <!-- Aquí estaba el botón "Ver todo" y fue eliminado -->
            </div>
 
            <div class="activity-list">
 
                @forelse ($operacionesRecientes as $operacion)
                    <div class="activity-item">
 
                        <span class="activity-dot activity-dot--{{ $operacion['tipo'] === 'venta' ? 'venta' : 'otro' }}"></span>
 
                        <div>
                            <strong>
                                {{ $operacion['titulo'] }}
                            </strong>
 
                            <span class="activity-detail">
                                {{ $operacion['detalle'] }}
                            </span>
 
                            <span>
                                {{ $operacion['fecha'] }}
                            </span>
                        </div>
 
                    </div>
                @empty
                    <div class="activity-item">
                        <div>
                            <span>No hay operaciones recientes por el momento.</span>
                        </div>
                    </div>
                @endforelse
 
            </div>
 
        </article>
 
    </section>
 
@endsection