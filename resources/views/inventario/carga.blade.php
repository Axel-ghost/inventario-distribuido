@extends('layouts.app')

@section('title', 'Carga masiva')
@section('header_title', 'Carga masiva de inventario')
@section('header_subtitle', 'Importa productos desde un archivo de Excel o XML')

@section('content')

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 16px; border-radius: 12px; margin-bottom: 24px;">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="carga-layout">
        
        <!-- FORMULARIO -->
        <form id="formCarga" method="POST" action="/inventario/carga" enctype="multipart/form-data" class="form-card" style="max-width: 100%;">
            @csrf

            <div class="input-group">
                <label for="archivo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Archivo (.xlsx o .xml, máximo 2 MB)
                </label>
                <input type="file" id="archivo" name="archivo" accept=".xlsx,.xml" required>
            </div>

            @if (Auth::user()->agencia_id === null)
                <div class="input-group">
                    <label for="agencia_id">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Agencia que recibe el inventario
                    </label>
                    <select id="agencia_id" name="agencia_id" required>
                        <option value="">Elige una agencia</option>
                        @foreach ($agencias as $agencia)
                            <option value="{{ $agencia->id }}" @selected((string) old('agencia_id') === (string) $agencia->id)>{{ $agencia->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="input-group">
                    <label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Agencia que recibe el inventario
                    </label>
                    <input type="text" value="{{ Auth::user()->agencia?->nombre }}" disabled style="opacity: 0.7;">
                </div>
            @endif

            <div class="input-group">
                <label for="modo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                    Si el número de parte ya existe
                </label>
                <select id="modo" name="modo" required>
                    <option value="reemplazar" @selected(old('modo', 'reemplazar') === 'reemplazar')>Reemplazar la existencia (es un conteo)</option>
                    <option value="sumar" @selected(old('modo') === 'sumar')>Sumar a la existencia (llegó mercancía)</option>
                </select>
            </div>

            <div class="input-group">
                <label for="stock_minimo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Stock mínimo para productos nuevos
                </label>
                <input type="number" id="stock_minimo" name="stock_minimo" min="0" value="{{ old('stock_minimo', 2) }}" required>
            </div>

            <!-- Contenedor de Botones (Cancelar a la izquierda, Revisar a la derecha) -->
            <div style="display: flex; gap: 12px; margin-top: auto;">
                <button type="button" class="btn-cancel" onclick="document.getElementById('formCarga').reset()" style="flex: 1;">
                    Cancelar
                </button>
                <button type="submit" class="btn-process" style="flex: 1;">
                    <span class="btn-label">Revisar archivo</span>
                </button>
            </div>
        </form>

        <!-- PANEL DERECHO -->
        <article class="panel" style="height: 100%;">
            <div class="panel-header">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <div style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <span style="font-size: 9px; color: #94a3b8; letter-spacing: 2px;">FORMATO</span>
                        <h3>Cómo debe venir el archivo</h3>
                    </div>
                </div>
            </div>

            <div style="color: #e2e8f0; font-size: 14px; line-height: 1.6; margin-top: 15px;">
                <p style="margin-bottom: 10px;">
                    <strong>Excel:</strong> la primera hoja, con una fila de encabezados. Se leen estas columnas por su nombre:
                </p>
                <ul style="margin: 0 0 14px 20px;">
                    <li><strong>NO. DE PARTE</strong> (código del producto)</li>
                    <li><strong>DESCRIPCION DE LA PIEZA</strong></li>
                    <li><strong>CANTIDAD</strong></li>
                    <li><strong>PRECIO DE COMPRA</strong></li>
                    <li><strong>PRECIO PUBLICO</strong> (lo que paga el cliente)</li>
                    <li>Opcionales: CATEGORIA, PROVEEDOR, STOCK MINIMO</li>
                </ul>
                <p style="margin-bottom: 20px; opacity: 0.8; font-size: 13px;">
                    La columna «PRECIO DE VENTA» no se usa. Los encabezados repetidos y las filas vacías se ignoran.
                </p>

                <div class="xml-box">
                    <div>
                        <strong style="display: block; color: #fff; margin-bottom: 4px;">Formato XML</strong>
                        <span style="font-size: 12px; color: #94a3b8;">Cada producto va en un &lt;producto&gt;</span>
                    </div>
                    <a href="/inventario/carga/ejemplo.xml" class="btn-descargar-xml">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Descargar ejemplo
                    </a>
                </div>
            </div>
        </article>

    </div>
@endsection