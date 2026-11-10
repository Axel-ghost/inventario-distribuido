@extends('layouts.app')

@section('title', 'Usuarios')

@section('header_title', 'Usuarios')
@section('header_subtitle', 'Administración de usuarios y roles')

@section('content')

    @if ($errors->any())
        <p style="color: #dc2626; margin-bottom: 16px; font-weight: 500;">{{ $errors->first() }}</p>
    @endif

    <div class="table-actions-top">
        <a href="/usuarios/crear" class="btn-process">
            <span class="btn-label">+ Nuevo usuario</span>
        </a>
    </div>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>USUARIO</th>
                    <th>CORREO</th>
                    <th>ROL</th>
                    <th>AGENCIA</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $u)
                    @php
                        if ($u->agencia) {
                            $textoAgencia = $u->agencia->nombre;
                        } elseif ($u->rol === 'cliente') {
                            $textoAgencia = 'Cliente en línea';
                        } elseif ($u->rol === 'administrador') {
                            $textoAgencia = 'Todas (dueño)';
                        } else {
                            $textoAgencia = 'Sin agencia';
                        }
                    @endphp
                    <tr>
                        <td>{{ $u->name }} {{ $u->apellidos }}</td>
                        <td>{{ $u->usuario }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ ucfirst($u->rol) }}</td>
                        <td @if ($textoAgencia === 'Sin agencia') style="color: #dc2626; font-weight: 500;" @endif>{{ $textoAgencia }}</td>
                        <td>
                            @if ($u->activo)
                                <span class="status-badge status-success">Activo</span>
                            @else
                                <span class="status-badge status-danger">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-wrapper">
                                <a href="/usuarios/{{ $u->id }}/editar" class="btn-action btn-edit" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                @if ($u->id !== Auth::id())
                                    <form method="POST" action="/usuarios/{{ $u->id }}/estado" style="margin: 0; display: flex;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-action {{ $u->activo ? 'btn-disable' : 'btn-enable' }}" title="{{ $u->activo ? 'Desactivar' : 'Activar' }}">
                                            @if ($u->activo)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection