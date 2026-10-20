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
                    <p>{{ Auth::user()->rol === 'administrador' ? 'Todas las requisiciones' : 'Tus requisiciones de reabasto' }}</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            @if (Auth::user()->rol === 'almacen')
                <a href="/pedidos/crear" class="btn-process" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; max-width: 220px; margin-bottom: 20px;">
                    <span class="btn-label">+ Nueva requisición</span>
                </a>
            @endif

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Productos</th>
                        <th>Estado</th>
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
                                    $colores = [
                                        'solicitado' => 'badge-warning',
                                        'en_revision' => 'badge-warning',
                                        'aprobado' => 'badge-ok',
                                        'rechazado' => 'badge-danger',
                                        'finalizado' => 'badge-ok',
                                    ];
                                    $etiquetas = [
                                        'solicitado' => 'Solicitado',
                                        'en_revision' => 'En revisión',
                                        'aprobado' => 'Aprobado',
                                        'rechazado' => 'Rechazado',
                                        'finalizado' => 'Finalizado',
                                    ];
                                @endphp
                                <span class="badge {{ $colores[$pedido->estado] }}">{{ $etiquetas[$pedido->estado] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: var(--muted);">No hay requisiciones todavía</td></tr>
                    @endforelse
                </tbody>
            </table>

        </main>

    </div>

</body>

</html>