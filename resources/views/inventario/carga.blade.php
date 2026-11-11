@extends('layouts.app')

@section('title', 'Carga masiva')

@section('header_title', 'Carga masiva de inventario')
@section('header_subtitle', 'Importa productos desde un archivo de Excel o XML')

@section('content')

    @if ($errors->any())
        <div style="color: #ef4444; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="/inventario/carga" enctype="multipart/form-data" class="form-card">
        @csrf

        <div class="input-group">
            <label for="archivo">Archivo (.xlsx o .xml, máximo 2 MB)</label>
            <input type="file" id="archivo" name="archivo" accept=".xlsx,.xml" required>
        </div>

        @if (Auth::user()->agencia_id === null)
            <div class="input-group">
                <label for="agencia_id">Agencia que recibe el inventario</label>
                <select id="agencia_id" name="agencia_id" required>
                    <option value="">Elige una agencia</option>
                    @foreach ($agencias as $agencia)
                        <option value="{{ $agencia->id }}" @selected((string) old('agencia_id') === (string) $agencia->id)>{{ $agencia->nombre }}</option>
                    @endforeach
                </select>
            </div>
        @else
            <div class="input-group">
                <label>Agencia que recibe el inventario</label>
                <input type="text" value="{{ Auth::user()->agencia?->nombre }}" disabled>
            </div>
        @endif

        <div class="input-group">
            <label for="modo">Si el número de parte ya existe en la agencia</label>
            <select id="modo" name="modo" required>
                <option value="reemplazar" @selected(old('modo', 'reemplazar') === 'reemplazar')>Reemplazar la existencia (es un conteo de inventario)</option>
                <option value="sumar" @selected(old('modo') === 'sumar')>Sumar a la existencia (llegó mercancía nueva)</option>
            </select>
        </div>

        <div class="input-group">
            <label for="stock_minimo">Stock mínimo para los productos nuevos</label>
            <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="{{ old('stock_minimo', 2) }}" required>
        </div>

        <button type="submit" class="btn-process">
            <span class="btn-label">Revisar archivo</span>
        </button>

    </form>

    <article class="panel" style="margin-top: 24px;">

        <div class="panel-header">
            <div>
                <span>FORMATO</span>
                <h3>Cómo debe venir el archivo</h3>
            </div>
        </div>

        <p style="margin-bottom: 10px;">
            <strong>Excel:</strong> la primera hoja, con una fila de encabezados. Se leen estas columnas por su nombre,
            sin importar en qué lugar estén:
        </p>
        <ul style="margin: 0 0 14px 20px; line-height: 1.7;">
            <li><strong>NO. DE PARTE</strong> (código del producto)</li>
            <li><strong>DESCRIPCION DE LA PIEZA</strong></li>
            <li><strong>CANTIDAD</strong></li>
            <li><strong>PRECIO DE COMPRA</strong></li>
            <li><strong>PRECIO PUBLICO</strong> (lo que paga el cliente)</li>
            <li>Opcionales: CATEGORIA, PROVEEDOR, STOCK MINIMO</li>
        </ul>
        <p style="margin-bottom: 14px; opacity: .8;">
            La columna «PRECIO DE VENTA» (compra + IVA) no se usa. Los encabezados repetidos y las filas vacías se ignoran,
            y si un número de parte aparece varias veces se suman sus cantidades.
        </p>

        <p>
            <strong>XML:</strong> cada producto va dentro de un elemento &lt;producto&gt; con sus campos.
            <a href="/inventario/carga/ejemplo.xml" style="text-decoration: underline;">Descargar ejemplo en XML</a>
        </p>

    </article>

@endsection