@extends('layouts.app')

@section('title', 'Revisar carga')

@section('header_title', 'Revisar carga de inventario')
@section('header_subtitle', $borrador['archivo'] . ' → ' . ($agencia?->nombre ?? 'Agencia'))

@section('content')

    @if ($errors->any())
        <div style="color: #ef4444; margin-bottom: 16px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <p style="margin-bottom: 16px; opacity: .85;">
        Todavía no se guardó nada. Modo:
        <strong>{{ $borrador['modo'] === 'sumar' ? 'sumar a la existencia actual' : 'reemplazar la existencia (conteo)' }}</strong>.
        Los productos que no estén en el archivo no se modifican.
    </p>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">◈</div>
            <div>
                <span>Productos nuevos</span>
                <strong>{{ $resumen['nuevos'] }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▤</div>
            <div>
                <span>Se actualizarán</span>
                <strong>{{ $resumen['actualizan'] }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▱</div>
            <div>
                <span>Filas con error (no entran)</span>
                <strong>{{ $resumen['errores'] }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▰</div>
            <div>
                <span>Productos con aviso</span>
                <strong>{{ $resumen['avisos'] }}</strong>
            </div>
        </article>
    </section>

    <section class="stats-grid" style="margin-top: 16px;">
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▣</div>
            <div>
                <span>Unidades en el archivo</span>
                <strong>{{ number_format($resumen['unidades']) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▣</div>
            <div>
                <span>Inversión (cantidad × compra)</span>
                <strong>${{ number_format($resumen['inversion'], 2) }}</strong>
            </div>
        </article>
    </section>

    @if (count($errores) > 0)
        <h3 style="margin: 28px 0 12px;">Filas con error</h3>

        <div class="table-container">
            <table class="data-table-clean">
                <thead>
                    <tr>
                        <th>FILA</th>
                        <th>NO. DE PARTE</th>
                        <th>PROBLEMA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($errores as $e)
                        <tr>
                            <td>{{ $e['fila'] }}</td>
                            <td>{{ $e['codigo'] !== '' ? $e['codigo'] : '—' }}</td>
                            <td>{{ $e['mensaje'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h3 style="margin: 28px 0 12px;">Productos a cargar</h3>

    <div class="table-container">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>ESTADO</th>
                    <th>NO. DE PARTE</th>
                    <th>DESCRIPCIÓN</th>
                    <th>CANTIDAD EN ARCHIVO</th>
                    <th>EXISTENCIA RESULTANTE</th>
                    <th>COMPRA</th>
                    <th>PÚBLICO</th>
                    <th>AVISOS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i)
                    <tr>
                        <td>
                            @if ($i['estado'] === 'nuevo')
                                <span class="status-badge status-success">Nuevo</span>
                            @else
                                <span class="status-badge" style="background: rgba(59, 130, 246, .2);">Actualiza</span>
                            @endif
                        </td>
                        <td>{{ $i['codigo'] }}</td>
                        <td>{{ $i['nombre'] }}</td>
                        <td>{{ $i['existencia'] }}</td>
                        <td>
                            @if ($i['estado'] === 'actualiza')
                                {{ $i['existencia_actual'] }} → <strong>{{ $i['existencia_final'] }}</strong>
                            @else
                                {{ $i['existencia_final'] }}
                            @endif
                        </td>
                        <td>${{ number_format($i['precio_compra'], 2) }}</td>
                        <td>${{ number_format($i['precio_venta'], 2) }}</td>
                        <td>
                            @forelse ($i['avisos'] as $aviso)
                                <div style="font-size: 13px; color: #d97706;">{{ $aviso }}</div>
                            @empty
                                —
                            @endforelse
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-top: 24px;">
        <form method="POST" action="/inventario/carga/{{ $token }}" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-process" style="width: auto;">
                <span class="btn-label">Confirmar y cargar {{ count($items) }} productos</span>
            </button>
        </form>

        <a href="/inventario/carga" style="text-decoration: underline;">Cancelar y subir otro archivo</a>
    </div>

@endsection