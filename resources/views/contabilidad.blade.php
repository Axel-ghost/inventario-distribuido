@extends('layouts.app')

@section('title', 'Contabilidad')

@section('header_title', 'Contabilidad')
@section('header_subtitle', 'Inversión, ventas, ganancia y stock que no se mueve')

@section('content')

    <style>
        .solo-impresion { display: none; }

        .filtro-link {
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, .45);
            text-decoration: none;
            color: inherit;
            font-size: 14px;
        }

        .filtro-link.activo {
            background: rgba(59, 130, 246, .25);
            border-color: rgba(59, 130, 246, .7);
            font-weight: 600;
        }

        .seccion-titulo { margin: 28px 0 12px; }

        @media print {
            body::before { display: none !important; }
            html, body { background: #fff !important; }
            * { color: #000 !important; text-shadow: none !important; }

            .sidebar, .dashboard-header, .no-print { display: none !important; }
            .solo-impresion { display: block !important; margin-bottom: 16px; }

            .dashboard { display: block !important; }
            .dashboard-content { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }

            .stat-card, .panel, .table-container {
                background: #fff !important;
                border: 1px solid #bbb !important;
                box-shadow: none !important;
                backdrop-filter: none !important;
            }

            .data-table-clean th, .data-table-clean td { border-bottom: 1px solid #ccc !important; }
        }
    </style>

    <div class="solo-impresion">
        <h2>Reporte de contabilidad — {{ Auth::user()->agencia?->nombre ?? 'Todas las agencias' }}</h2>
        <p>{{ $etiqueta }} · Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="no-print" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 8px;">
        <a href="/contabilidad?periodo=semana&dias={{ $dias }}" class="filtro-link {{ $periodo === 'semana' ? 'activo' : '' }}">Esta semana</a>
        <a href="/contabilidad?periodo=mes&dias={{ $dias }}" class="filtro-link {{ $periodo === 'mes' ? 'activo' : '' }}">Este mes</a>

        <button type="button" onclick="window.print()" class="btn-process" style="width: auto; margin-left: auto;">
            <span class="btn-label">Imprimir reporte</span>
        </button>
    </div>

    {{-- ---------- INVENTARIO ---------- --}}
    <h3 class="seccion-titulo">Inventario actual</h3>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▣</div>
            <div>
                <span>Inversión en inventario</span>
                <strong>${{ number_format($inversion, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▰</div>
            <div>
                <span>Valor de venta</span>
                <strong>${{ number_format($valorVenta, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">◈</div>
            <div>
                <span>Ganancia potencial</span>
                <strong>${{ number_format($gananciaPotencial, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▤</div>
            <div>
                <span>Unidades en stock</span>
                <strong>{{ number_format($unidades) }}</strong>
            </div>
        </article>
    </section>

    {{-- ---------- VENTAS DEL PERIODO ---------- --}}
    <h3 class="seccion-titulo">Ventas — {{ $etiqueta }}</h3>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▰</div>
            <div>
                <span>Vendido</span>
                <strong>${{ number_format($totalVendido, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▣</div>
            <div>
                <span>Costo de lo vendido</span>
                <strong>${{ number_format($costoVendido, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">◈</div>
            <div>
                <span>Ganancia realizada</span>
                <strong>${{ number_format($gananciaRealizada, 2) }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▱</div>
            <div>
                <span>Ventas realizadas</span>
                <strong>{{ $ventas->count() }}</strong>
            </div>
        </article>
    </section>

    <div class="table-container" style="margin-top: 16px;">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>FOLIO</th>
                    <th>FECHA</th>
                    <th>REGISTRÓ</th>
                    <th>VENTA</th>
                    <th>COSTO</th>
                    <th>GANANCIA</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ventas as $v)
                    @php
                        $costoVenta = $v->detalles->sum(fn ($d) => $d->cantidad * $d->costo_unitario);
                    @endphp
                    <tr>
                        <td><a href="/ventas/{{ $v->id }}" style="color: inherit; text-decoration: underline;">{{ $v->folio }}</a></td>
                        <td>{{ $v->fecha_venta?->format('d/m/Y H:i') }}</td>
                        <td>{{ $v->usuario?->name ?? '—' }}</td>
                        <td>${{ number_format($v->total, 2) }}</td>
                        <td>${{ number_format($costoVenta, 2) }}</td>
                        <td>${{ number_format($v->total - $costoVenta, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No hay ventas en este periodo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ---------- STOCK QUE NO SE MUEVE ---------- --}}
    <h3 class="seccion-titulo">Stock que no se mueve</h3>

    <form method="GET" action="/contabilidad" class="no-print" style="display: flex; gap: 10px; align-items: center; margin-bottom: 12px;">
        <input type="hidden" name="periodo" value="{{ $periodo }}">
        <label for="dias">Sin ventas en los últimos</label>
        
        <!-- Aquí apliqué la clase "select-verde" -->
        <select id="dias" name="dias" onchange="this.form.submit()" class="select-verde">
            @foreach ([30, 60, 90, 180, 365] as $opcion)
                <option value="{{ $opcion }}" @selected($dias === $opcion)>{{ $opcion }} días</option>
            @endforeach
        </select>
    </form>

    <p class="solo-impresion">Productos sin ventas en los últimos {{ $dias }} días.</p>

    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▤</div>
            <div>
                <span>Productos sin movimiento</span>
                <strong>{{ $stockMuerto->count() }}</strong>
            </div>
        </article>

        <article class="stat-card">
            <div class="stat-icon" aria-hidden="true">▣</div>
            <div>
                <span>Capital inmovilizado</span>
                <strong>${{ number_format($capitalInmovilizado, 2) }}</strong>
            </div>
        </article>
    </section>

    <div class="table-container" style="margin-top: 16px;">
        <table class="data-table-clean">
            <thead>
                <tr>
                    <th>#</th> <!-- NUEVA COLUMNA -->
                    <th>CÓDIGO</th>
                    <th>PRODUCTO</th>
                    <th>EXISTENCIA</th>
                    <th>COSTO UNIT.</th>
                    <th>CAPITAL INMOVILIZADO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockMuerto as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- NUEVA COLUMNA CON EL CONTADOR -->
                        <td>{{ $p->codigo }}</td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->existencia }}</td>
                        <td>${{ number_format($p->precio_compra, 2) }}</td>
                        <td>${{ number_format($p->existencia * $p->precio_compra, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Todo el inventario con existencia tuvo ventas en este lapso.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection