@extends('layouts.app')

@section('title', 'Pedidos')

@section('header_title', 'Pedidos')
@section('header_subtitle', Auth::user()->rol === 'administrador' ? 'Todas las requisiciones' : 'Tus requisiciones de reabasto')

@section('content')

    <div class="table-actions-top">
        @if (Auth::user()->rol === 'almacen')
            <a href="/pedidos/crear" class="btn-process">
                <span class="btn-label">+ Nueva requisición</span>
            </a>
        @endif
    </div>

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

@endsection