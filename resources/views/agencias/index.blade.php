@extends('layouts.app')

@section('title', 'Agencias')

@section('header_title', 'Agencias')
@section('header_subtitle', 'Sucursales con sus propios datos')

@section('content')

    <div class="table-actions-top">
        <a href="/agencias/crear" class="btn-process">
            <span class="btn-label">+ Nueva agencia</span>
        </a>
    </div>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>AGENCIA</th>
                    <th>CIUDAD</th>
                    <th>USUARIOS</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agencias as $agencia)
                    <tr>
                        <td>{{ $agencia->nombre }}</td>
                        <td>{{ $agencia->ciudad ?? '—' }}</td>
                        <td>{{ $agencia->usuarios_count }}</td>
                        <td>
                            @if ($agencia->activo)
                                <span class="status-badge status-success">Activa</span>
                            @else
                                <span class="status-badge status-danger">Inactiva</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Todavía no hay agencias registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection