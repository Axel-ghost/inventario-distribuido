<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CoreBuild Gaming | Dashboard</title>

    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <!-- SIDEBAR -->
        <x-sidebar active-page="dashboard" />

        <!-- CONTENIDO -->
        <main class="dashboard-content">

            <!-- HEADER -->
            <header class="dashboard-header">
                <div>
                    <p class="page-label">PANEL DE CONTROL</p>
                    <h2>Dashboard</h2>
                </div>

                <x-profile-card />
            </header>

            <!-- BIENVENIDA -->
            <section class="welcome-section">
                <div>
                    <p class="welcome-label">SISTEMA CoreBuild GAMING</p>
                    <h3>Bienvenido de nuevo, {{ Auth::user()->name }}</h3>
                    <p>Aquí tienes un resumen del estado actual de tu inventario y operaciones.</p>
                </div>
            </section>

            <!-- ESTADÍSTICAS -->
            <section class="stats-grid">

                <article class="stat-card">
                    <div class="stat-icon">◈</div>
                    <div>
                        <span>Productos</span>
                        <strong>{{ number_format($totalProductos) }}</strong>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">▤</div>
                    <div>
                        <span>Stock bajo</span>
                        <strong>{{ number_format($stockBajo) }}</strong>
                    </div>
                </article>

    <article class="stat-card">
        <div class="stat-icon">▨</div>
        <div>
            <span>Agotados</span>
            <strong>{{ number_format($productosAgotados) }}</strong>
        </div>
    </article>

                <article class="stat-card">
                    <div class="stat-icon">▰</div>
                    <div>
                        <span>Ventas del mes</span>
                        <strong>${{ number_format($ventasDelMes, 0) }}</strong>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">▱</div>
                    <div>
                        <span>Pedidos pendientes</span>
                        <strong>{{ number_format($pedidosPendientes) }}</strong>
                    </div>
                </article>

            </section>

            <!-- CONTENIDO INFERIOR -->
            <section class="dashboard-grid">

                <!-- STOCK BAJO -->
                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <span>INVENTARIO</span>
                            <h3>Stock bajo</h3>
                        </div>
                        <a href="/inventario" style="text-decoration: none;">
                            <button>Ver todo</button>
                        </a>
                    </div>

                    <div class="inventory-list">
                        @forelse ($productosStockBajo as $producto)
                            <div class="inventory-item">
                                <div>
                                    <strong>{{ $producto->nombre }}</strong>
                                    <span>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</span>
                                </div>
                                <strong class="stock-warning">
                                    {{ $producto->existencia }} unidades
                                </strong>
                            </div>
                        @empty
                            <div class="inventory-item" style="justify-content: center;">
                                <span style="color: #888;">No hay productos con stock bajo.</span>
                            </div>
                        @endforelse
                    </div>
                </article>

                <!-- ACTIVIDAD -->
                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <span>ACTIVIDAD</span>
                            <h3>Operaciones recientes</h3>
                        </div>
                    </div>

                    <div class="activity-list">
                        @forelse ($operacionesRecientes as $op)
                            <div class="activity-item">
                                <span class="activity-dot"></span>
                                <div>
                                    <strong>
                                        @if ($op->tipo === 'salida')
                                            Salida de stock: {{ $op->producto->nombre ?? '—' }}
                                        @elseif ($op->tipo === 'entrada')
                                            Entrada de stock: {{ $op->producto->nombre ?? '—' }}
                                        @else
                                            {{ ucfirst($op->tipo) }}: {{ $op->producto->nombre ?? '—' }}
                                        @endif
                                    </strong>
                                    <span>
                                        {{ $op->created_at->diffForHumans() }}
                                        @if ($op->motivo)
                                            · {{ $op->motivo }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="activity-item" style="justify-content: center;">
                                <span style="color: #888;">No hay operaciones recientes.</span>
                            </div>
                        @endforelse
                    </div>
                </article>

            </section>

        </main>

    </div>

</body>

</html>