@extends('layouts.app')

@section('title', 'Pedido ' . $pedido->folio)

@section('header_title', $pedido->folio)
@section('header_subtitle', 'Solicitado por ' . $pedido->solicitante->name . ' ' . $pedido->solicitante->apellidos)

@section('content')

    @if (session('status'))
        <p style="color: #22c55e; margin-bottom: 24px; font-weight: 500;">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p style="color: #dc2626; margin-bottom: 24px; font-weight: 500;">{{ $errors->first() }}</p>
    @endif

    <!-- TIMELINE -->
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

        <div class="form-card" style="margin-bottom: 24px; max-width: 100%;">
            <strong style="color: #dc2626;">Motivo del rechazo:</strong>
            <p style="margin-top: 8px;">{{ $pedido->comentario_admin }}</p>
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

    <!-- TABLA DE PRODUCTOS -->
    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CÓDIGO</th>
                    <th>CANTIDAD SOLICITADA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido->detalles as $detalle)
                    <tr>
                        <td style="font-weight: 500;">{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->producto->codigo }}</td>
                        <td>{{ $detalle->cantidad_solicitada }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 32px; align-items: center;">
        @if (Auth::user()->rol === 'administrador' && $pedido->estado === 'solicitado')
            <form method="POST" action="/pedidos/{{ $pedido->id }}/revisar">
                @csrf @method('PATCH')
                <button type="submit" class="btn-process" style="max-width: 200px; height: 44px; border-radius: 8px;">
                    <span class="btn-label">Poner en revisión</span>
                </button>
            </form>
        @endif

        @if (Auth::user()->rol === 'administrador' && $pedido->estado === 'en_revision')
            <form method="POST" action="/pedidos/{{ $pedido->id }}/aprobar">
                @csrf @method('PATCH')
                <button type="submit" class="btn-process" style="max-width: 160px; height: 44px; border-radius: 8px;">
                    <span class="btn-label">Aprobar</span>
                </button>
            </form>

            <form method="POST" action="/pedidos/{{ $pedido->id }}/rechazar" style="display: flex; gap: 8px;">
                @csrf @method('PATCH')
                
                <input type="text" name="comentario_admin" placeholder="Motivo del rechazo" required
                       style="padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.2); background: rgba(0, 0, 0, 0.3); color: #ffffff; font-size: 14px; outline: none;">
                
                <button type="submit" class="btn-action" style="padding: 0 16px; border-radius: 8px; font-weight: 600; background: rgba(220, 38, 38, 0.8); color: #ffffff; border: none;">
                    Rechazar
                </button>
            </form>
        @endif

        @if (in_array(Auth::user()->rol, ['administrador', 'almacen']) && $pedido->estado === 'aprobado')
            <form method="POST" action="/pedidos/{{ $pedido->id }}/finalizar">
                @csrf @method('PATCH')
                <button type="submit" class="btn-process" style="max-width: 260px; height: 44px; border-radius: 8px;">
                    <span class="btn-label">Marcar como recibido / Finalizar</span>
                </button>
            </form>
        @endif
    </div>

    <!-- BOTÓN VOLVER -->
    <div>
        <a href="/pedidos" class="catalog-back-button" style="width: auto; min-width: 200px; display: inline-flex;">
            &larr; Volver a pedidos
        </a>
    </div>

@endsection