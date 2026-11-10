@extends('layouts.app')

@section('title', 'CoreBuild | Editar usuario')

@section('header_title', 'Editar usuario')
@section('header_subtitle', $usuario->name . ' ' . $usuario->apellidos)

@section('content')

    @if ($errors->any())
        <div style="color: #ef4444; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/usuarios/{{ $usuario->id }}" class="form-card">
        @csrf
        @method('PUT')

        <div class="input-group">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
        </div>

        <div class="input-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}" required>
        </div>

        <div class="input-group">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $usuario->usuario) }}" required>
        </div>

        <div class="input-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
        </div>

        <div class="input-group">
            <label for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password" placeholder="Déjalo en blanco para no cambiarla">
        </div>

        <div class="input-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="administrador" @selected(old('rol', $usuario->rol) === 'administrador')>Administrador</option>
                <option value="vendedor" @selected(old('rol', $usuario->rol) === 'vendedor')>Vendedor</option>
                <option value="caja" @selected(old('rol', $usuario->rol) === 'caja')>Caja</option>
                <option value="almacen" @selected(old('rol', $usuario->rol) === 'almacen')>Almacén</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="/usuarios" class="btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="btn-process">
                <span class="btn-label">Guardar cambios</span>
            </button>
        </div>

    </form>

@endsection