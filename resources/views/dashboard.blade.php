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
                    <p class="page-label">
                        PANEL DE CONTROL
                    </p>

                    <h2>
                        Dashboard
                    </h2>
                </div>


                <x-profile-card />

            </header>


            <!-- BIENVENIDA -->
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


            <!-- ESTADÍSTICAS -->
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


            <!-- CONTENIDO INFERIOR -->
            <section class="dashboard-grid">


                <!-- STOCK BAJO -->
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


                <!-- ACTIVIDAD -->
                <article class="panel">

                    <div class="panel-header">

                        <div>
                            <span>ACTIVIDAD</span>

                            <h3>
                                Operaciones recientes
                            </h3>
                        </div>

                    </div>


                    <div class="activity-list">

                        <div class="activity-item">

                            <span class="activity-dot"></span>

                            <div>
                                <strong>
                                    Nueva venta registrada
                                </strong>

                                <span>
                                    Hace 10 minutos
                                </span>
                            </div>

                        </div>


                        <div class="activity-item">

                            <span class="activity-dot"></span>

                            <div>
                                <strong>
                                    Pedido autorizado
                                </strong>

                                <span>
                                    Hace 25 minutos
                                </span>
                            </div>

                        </div>


                        <div class="activity-item">

                            <span class="activity-dot"></span>

                            <div>
                                <strong>
                                    Producto actualizado
                                </strong>

                                <span>
                                    Hace 1 hora
                                </span>
                            </div>

                        </div>

                    </div>

                </article>

            </section>

        </main>

    </div>

</body>

</html>