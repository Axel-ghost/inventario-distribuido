@extends('layouts.app')

@section('title', 'CoreBuild | Nuevo usuario')

@section('header_title', 'Nuevo usuario')
@section('header_subtitle', 'Registra un usuario y asígnale un rol')

@section('content')

    @if ($errors->any())
        <div style="color: #ef4444; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/usuarios" class="form-card">
        @csrf

        <div class="input-group">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="input-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
        </div>

        <div class="input-group">
            <label for="usuario">Usuario</label>
            <input type="text" id="usuario" name="usuario" value="{{ old('usuario') }}" required>
        </div>

        <div class="input-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="input-group">
            <label for="password">Contraseña (mínimo 8 caracteres)</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="input-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="administrador" @selected(old('rol') === 'administrador')>Administrador</option>
                <option value="vendedor" @selected(old('rol') === 'vendedor')>Vendedor</option>
                <option value="caja" @selected(old('rol') === 'caja')>Caja</option>
                <option value="almacen" @selected(old('rol') === 'almacen')>Almacén</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="/usuarios" class="btn-cancel">
                Cancelar
            </a>
            <button type="submit" class="btn-process">
                <span class="btn-label">Registrar usuario</span>
            </button>
        </div>

    </form>

@endsection