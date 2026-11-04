<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CoreBuild Gaming | Dashboard</title>

    @vite(['resources/css/app.css', 'resources/css/dark.css', 'resources/js/app.js'])

    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = saved || (prefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="dashboard" />

        <main class="dashboard-content">

            <header class="dashboard-header">

                <div>
                    <p class="page-label">
                        PANEL DE CONTROL
                    </p>

                    <h2>
                        Dashboard
                    </h2>
                </div>

                <x-profile-card />

            </header>

            <section class="welcome-section">

                <div>
                    <p class="welcome-label">
                        SISTEMA CoreBuild GAMING
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
                    <div class="stat-icon">
                        ◈
                    </div>
                    <div>
                        <span>Productos</span>
                        <strong>{{ $totalProductos }}</strong>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">
                        ▤
                    </div>
                    <div>
                        <span>Stock bajo</span>
                        <strong>{{ $stockBajo }}</strong>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">
                        ▰
                    </div>
                    <div>
                        <span>Ventas del mes</span>
                        <strong>${{ number_format($ventasDelMes, 2) }}</strong>
                    </div>
                </article>

                <article class="stat-card">
                    <div class="stat-icon">
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

                        <a href="/inventario">
                            <button type="button">
                                Ver todo
                            </button>
                        </a>
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

                        <a href="/actividades">
                            <button type="button">
                                Ver todo
                            </button>
                        </a>

                    </div>

                    <div class="activity-list">

                        @forelse ($operacionesRecientes as $operacion)
                            <div class="activity-item">

                                <span class="activity-dot" style="background-color: {{ $operacion['tipo'] === 'venta' ? '#10b981' : '#f59e0b' }};"></span>

                                <div>
                                    <strong>
                                        {{ $operacion['titulo'] }}
                                    </strong>
                                    
                                    <span style="display: block; font-size: 0.85em; color: var(--muted); margin-bottom: 2px;">
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

        </main>

    </div>

</body>

</html>