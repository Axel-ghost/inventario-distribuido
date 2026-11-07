@extends('layouts.app')

@section('title', 'Ventas')

@section('header_title', 'Ventas')
@section('header_subtitle', 'Historial de ventas registradas')

@section('content')

    <div class="table-actions-top">
        @if (in_array(Auth::user()->rol, ['administrador', 'vendedor']))
            <a href="/ventas/crear" class="btn-process">
                <span class="btn-label">+ Nueva venta</span>
            </a>
        @endif
    </div>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>FOLIO</th>
                    <th>FECHA</th>
                    <th>VENDEDOR</th>
                    <th>PRODUCTOS</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ventas as $venta)
                    <tr onclick="window.location='/ventas/{{ $venta->id }}'" style="cursor: pointer;">
                        <td>{{ $venta->folio }}</td>
                        <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                        <td>{{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</td>
                        <td>
                            @foreach ($venta->detalles as $detalle)
                                {{ $detalle->producto->nombre }} (x{{ $detalle->cantidad }}){{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td style="font-weight: 600;">${{ number_format($venta->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--muted);">
                            No hay ventas registradas todavía
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection