<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild | Pedido {{ $pedido->folio }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="pedidos" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>{{ $pedido->folio }}</h2>
                    <p>Solicitado por {{ $pedido->solicitante->name }} {{ $pedido->solicitante->apellidos }}</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <p style="color: #ef4444; margin-bottom: 16px;">{{ $errors->first() }}</p>
            @endif

            @if ($pedido->estado === 'rechazado')
                <div class="timeline">
                    <div class="timeline-step completo">
                        <span class="timeline-dot"></span>
                        <span>Solicitado</span>
                    </div>
                    <div class="timeline-step completo">
                        <span class="timeline-dot"></span>
                        <span>En revisión</span>
                    </div>
                    <div class="timeline-step rechazado">
                        <span class="timeline-dot"></span>
                        <span>Rechazado</span>
                    </div>
                </div>

                <div class="form-card" style="margin-bottom: 24px;">
                    <strong style="color: #ef4444;">Motivo del rechazo:</strong>
                    <p style="margin-top: 8px; color: var(--muted);">{{ $pedido->comentario_admin }}</p>
                </div>
            @else
                @php
                    $pasos = ['solicitado', 'en_revision', 'aprobado', 'finalizado'];
                    $indiceActual = array_search($pedido->estado, $pasos);
                @endphp

                <div class="timeline">
                    @foreach ($pasos as $i => $paso)
                        <div class="timeline-step {{ $i <= $indiceActual ? 'completo' : '' }}">
                            <span class="timeline-dot"></span>
                            <span>{{ ucfirst(str_replace('_', ' ', $paso)) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <table class="data-table" style="margin-bottom: 24px;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Cantidad solicitada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedido->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre }}</td>
                            <td>{{ $detalle->producto->codigo }}</td>
                            <td>{{ $detalle->cantidad_solicitada }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">

                @if (Auth::user()->rol === 'administrador' && $pedido->estado === 'solicitado')
                    <form method="POST" action="/pedidos/{{ $pedido->id }}/revisar">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-process" style="max-width: 200px;">
                            <span class="btn-label">Poner en revisión</span>
                        </button>
                    </form>
                @endif

                @if (Auth::user()->rol === 'administrador' && $pedido->estado === 'en_revision')
                    <form method="POST" action="/pedidos/{{ $pedido->id }}/aprobar">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-process" style="max-width: 160px;">
                            <span class="btn-label">Aprobar</span>
                        </button>
                    </form>

                    <form method="POST" action="/pedidos/{{ $pedido->id }}/rechazar" style="display: flex; gap: 8px;">
                        @csrf @method('PATCH')
                        <input type="text" name="comentario_admin" placeholder="Motivo del rechazo" required
                               style="padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--card); color: var(--text);">
                        <button type="submit" class="venta-quitar" style="border: 1px solid #ef4444; border-radius: 10px; padding: 0 16px;">
                            Rechazar
                        </button>
                    </form>
                @endif

                @if (in_array(Auth::user()->rol, ['administrador', 'almacen']) && $pedido->estado === 'aprobado')
                    <form method="POST" action="/pedidos/{{ $pedido->id }}/finalizar">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn-process" style="max-width: 260px;">
                            <span class="btn-label">Marcar como recibido / Finalizar</span>
                        </button>
                    </form>
                @endif

            </div>

                <div style="margin-top: 28px; margin-bottom: 15px;">
            <a href="/pedidos"
               style="
                   display: inline-flex;
                   align-items: center;
                   justify-content: center;
                   padding: 12px 20px;
                   border: 1px solid #60d5f8;
                   border-radius: 10px;
                   color: #60d5f8;
                   background: transparent;
                   text-decoration: none;
                   font-weight: 600;
                   transition: 0.3s;
               ">
                &larr; Volver a pedidos
            </a>
        </div>
</main>

    </div>

</body>

</html>