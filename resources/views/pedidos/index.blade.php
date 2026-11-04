<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Pedidos</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="pedidos" />

                <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>Pedidos</h2>
                    <p class="page-subtitle">{{ Auth::user()->rol === 'administrador' ? 'Todas las requisiciones' : 'Tus requisiciones de reabasto' }}</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px; font-weight: 500;">{{ session('status') }}</p>
            @endif

            <!-- Botón de Nueva Requisición (Estilo limpio) -->
            <div class="table-actions-top">
                @if (Auth::user()->rol === 'almacen')
                    <a href="/pedidos/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 200px; height: 44px; border-radius: 8px;">
                        <span class="btn-label">+ Nueva requisición</span>
                    </a>
                @endif
            </div>

            <!-- Contenedor de la Tabla -->
            <div class="table-container">
                <table class="data-table-clean">
                    <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>SOLICITANTE</th>
                            <th>FECHA</th>
                            <th>PRODUCTOS</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pedidos as $pedido)
                            <tr onclick="window.location='/pedidos/{{ $pedido->id }}'" style="cursor: pointer;">
                                <td>{{ $pedido->folio }}</td>
                                <td>{{ $pedido->solicitante->name }} {{ $pedido->solicitante->apellidos }}</td>
                                <td>{{ $pedido->fecha_solicitud->format('d/m/Y H:i') }}</td>
                                <td>{{ $pedido->detalles->count() }} producto(s)</td>
                                <td>
                                    @php
                                        // Mapeo de colores a las nuevas clases del diseño limpio
                                        $colores = [
                                            'solicitado' => 'status-warning',
                                            'en_revision' => 'status-warning',
                                            'aprobado' => 'status-success',
                                            'rechazado' => 'status-danger',
                                            'finalizado' => 'status-success',
                                        ];
                                        $etiquetas = [
                                            'solicitado' => 'Solicitado',
                                            'en_revision' => 'En revisión',
                                            'aprobado' => 'Aprobado',
                                            'rechazado' => 'Rechazado',
                                            'finalizado' => 'Finalizado',
                                        ];
                                    @endphp
                                    <span class="status-badge {{ $colores[$pedido->estado] }}">
                                        {{ $etiquetas[$pedido->estado] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: var(--muted);">
                                    No hay requisiciones todavía
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </main>

    </div>

</body>

</html>