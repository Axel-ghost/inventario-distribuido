@extends('layouts.app')

@section('title', 'Nueva agencia')

@section('header_title', 'Nueva agencia')
@section('header_subtitle', 'Registra una sucursal con sus propios datos')

@section('content')

    @if ($errors->any())
        <div style="color: #ef4444; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/agencias" class="form-card">
        @csrf

        <div class="input-group">
            <label for="nombre">Nombre de la agencia</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
        </div>

        <div class="input-group">
            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad') }}">
        </div>

        <button type="submit" class="btn-process">
            <span class="btn-label">Registrar agencia</span>
        </button>

    </form>

@endsection