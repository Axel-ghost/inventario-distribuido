@extends('layouts.app')

@section('title', 'Editar usuario')

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
                <option value="contabilidad" @selected(old('rol', $usuario->rol) === 'contabilidad')>Contabilidad</option>
                @if ($usuario->rol === 'cliente')
                    <option value="cliente" selected>Cliente</option>
                @endif
            </select>
        </div>

        @if (Auth::user()->agencia_id === null)
            <div class="input-group">
                <label for="agencia_id">Agencia</label>
                <select id="agencia_id" name="agencia_id" @disabled(Auth::id() === $usuario->id)>
                    <option value="">Todas las agencias (solo para administrador dueño)</option>
                    @foreach ($agencias as $agencia)
                        <option value="{{ $agencia->id }}" @selected((string) old('agencia_id', $usuario->agencia_id) === (string) $agencia->id)>{{ $agencia->nombre }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="input-group">
                <label>Agencia</label>
                <input type="text" value="{{ $usuario->agencia?->nombre ?? '—' }}" disabled>
            </div>
        @endif

        @if (Auth::id() === $usuario->id)
            <p style="opacity: .7; margin-bottom: 16px;">No puedes cambiar tu propio rol ni tu agencia.</p>
        @endif

        <button type="submit" class="btn-process">
            <span class="btn-label">Guardar cambios</span>
        </button>

    </form>

@endsection