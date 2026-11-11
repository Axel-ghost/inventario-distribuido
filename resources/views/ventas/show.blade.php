@extends('layouts.app')

@section('title', 'Venta ' . $venta->folio)

@section('header_title', $venta->folio)
@section('header_subtitle', 'Registrada por ' . $venta->usuario->name . ' ' . $venta->usuario->apellidos)

@section('content')

    <div class="ticket" id="ticket">

        <div class="ticket-header">
            <!-- Logo de Bajaj en lugar del texto -->
            <img src="{{ asset('images/logo.png') }}" alt="BAJAJ" style="max-height: 60px; object-fit: contain; margin: 0 auto 10px auto; display: block;">
            <p>Comprobante de venta</p>
        </div>

        <div class="ticket-meta">
            <div>
                <span>Folio</span>
                <strong>{{ $venta->folio }}</strong>
            </div>
            <div>
                <span>Fecha</span>
                <strong>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</strong>
            </div>
            <div>
                <span>Vendedor</span>
                <strong>{{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</strong>
            </div>
        </div>

        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ticket-total">
            <span>Total</span>
            <strong>${{ number_format($venta->total, 2) }}</strong>
        </div>

    </div>

    <div class="ticket-actions no-print">
        
        <a href="/ventas" class="catalog-back-button">
            &larr; Volver al historial
        </a>

        <button type="button" class="btn-process" onclick="window.print()">
            <span class="btn-label">Imprimir / Guardar PDF</span>
        </button>

    </div>

@endsection