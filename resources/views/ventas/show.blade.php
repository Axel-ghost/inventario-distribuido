<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreBuild_{{ $venta->folio }}</title>
    @vite(['resources/css/app.css'])

    <!-- COREBUILD_TICKET_STYLES_V1 -->
    <style>

        /* =========================================
           COMPROBANTE DE VENTA - PANTALLA
           ========================================= */

        .ticket {
            width: 100%;
            max-width: 850px;
            margin: 25px auto;
            padding: 35px;
            box-sizing: border-box;

            background: #101923;
            border: 1px solid rgba(96, 213, 248, 0.20);
            border-radius: 16px;

            color: #f1f5f9;
        }

        .ticket-header {
            text-align: center;
            padding-bottom: 25px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .ticket-header h3 {
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #60d5f8;
        }

        .ticket-header p {
            margin: 0;
            color: #94a3b8;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Datos del comprobante */

        .ticket-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .ticket-meta > div {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 0;
        }

        .ticket-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
        }

        .ticket-meta strong {
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            overflow-wrap: anywhere;
        }

        /* Tabla de productos */

        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            margin-bottom: 25px;
        }

        .ticket-table th,
        .ticket-table td {
            padding: 15px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.10);
            vertical-align: middle;
        }

        .ticket-table th {
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        .ticket-table td {
            color: #f1f5f9;
            font-size: 14px;
        }

        .ticket-table th:first-child,
        .ticket-table td:first-child {
            text-align: left;
            width: 45%;
            overflow-wrap: anywhere;
        }

        .ticket-table th:nth-child(2),
        .ticket-table td:nth-child(2) {
            text-align: center;
        }

        .ticket-table th:nth-child(3),
        .ticket-table td:nth-child(3),
        .ticket-table th:nth-child(4),
        .ticket-table td:nth-child(4) {
            text-align: right;
            white-space: nowrap;
        }

        /* Total */

        .ticket-total {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 25px;

            margin-top: 25px;
            padding-top: 20px;

            border-top: 2px solid rgba(96,213,248,0.35);
        }

        .ticket-total span {
            color: #94a3b8;
            font-size: 15px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .ticket-total strong {
            color: #60d5f8;
            font-size: 26px;
            font-weight: 800;
        }

        /* Botones */

        .ticket-actions {
            max-width: 850px;
            margin: 25px auto;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .ticket-actions .btn-process {
            width: auto;
            min-width: 220px;
        }

        .ticket-actions a {
            text-decoration: none;
        }

        /* Adaptacion para pantallas pequenas */

        @media screen and (max-width: 768px) {

            .ticket {
                padding: 20px;
                margin: 15px auto;
            }

            .ticket-meta {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .ticket-table {
                font-size: 12px;
            }

            .ticket-table th,
            .ticket-table td {
                padding: 10px 6px;
            }

            .ticket-total strong {
                font-size: 21px;
            }
        }

        /* =========================================
           IMPRESION / GUARDAR COMO PDF
           ========================================= */

        @page {
            size: letter;
            margin: 15mm;
        }

        @media print {

            html,
            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
            }

            /* Ocultar toda la interfaz administrativa */

            body * {
                visibility: hidden !important;
            }

            /* Mostrar unicamente el comprobante */

            #ticket,
            #ticket * {
                visibility: visible !important;
            }

            /* Evitar que el menu lateral afecte la posicion */

            .dashboard,
            .dashboard-content {
                display: block !important;
                position: static !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                background: #ffffff !important;
            }

            .sidebar,
            .dashboard-header,
            .ticket-actions,
            .no-print {
                display: none !important;
            }

            /* Comprobante */

            #ticket {
                display: block !important;
                position: static !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;

                background: #ffffff !important;
                color: #000000 !important;

                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .ticket-header {
                border-bottom: 2px solid #222222 !important;
                margin-bottom: 20px !important;
            }

            .ticket-header h3 {
                color: #000000 !important;
                font-size: 24px !important;
            }

            .ticket-header p {
                color: #444444 !important;
            }

            .ticket-meta span {
                color: #555555 !important;
            }

            .ticket-meta strong {
                color: #000000 !important;
            }

            .ticket-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .ticket-table th,
            .ticket-table td {
                color: #000000 !important;
                border-bottom: 1px solid #cccccc !important;
                padding: 12px 8px !important;
            }

            .ticket-table th {
                background: #eeeeee !important;
                font-weight: bold !important;
            }

            .ticket-total {
                border-top: 2px solid #222222 !important;
                margin-top: 20px !important;
                padding-top: 15px !important;
            }

            .ticket-total span,
            .ticket-total strong {
                color: #000000 !important;
            }

            .ticket-total strong {
                font-size: 24px !important;
            }

            /* Evitar cortes innecesarios */

            .ticket-header,
            .ticket-meta,
            .ticket-total,
            .ticket-table tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .ticket-table thead {
                display: table-header-group;
            }

            .ticket-table tr {
                break-inside: avoid;
            }
        }

    </style>
</head>

<body>

    <div class="dashboard">

        <x-sidebar active-page="ventas" />

        <main class="dashboard-content">

            <header class="dashboard-header">
                <div>
                    <h2>{{ $venta->folio }}</h2>
                    <p>Registrada por {{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</p>
                </div>

                <x-profile-card />
            </header>

            @if (session('status'))
                <p style="color: #22c55e; margin-bottom: 16px;">{{ session('status') }}</p>
            @endif

            <div class="ticket" id="ticket">

                <div class="ticket-header">
                    <h3>CoreBuild Gaming</h3>
                    <p>Comprobante de venta</p>
                </div>

                <div class="ticket-meta">
                    <div>
                        <span>Folio</span>
                        <strong>{{ $venta->folio }}</strong>
                    </div>
                    <div>
                        <span>Fecha</span>
                        <strong>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>
                        <span>Vendedor</span>
                        <strong>{{ $venta->usuario->name }} {{ $venta->usuario->apellidos }}</strong>
                    </div>
                </div>

                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="ticket-total">
                    <span>Total</span>
                    <strong>${{ number_format($venta->total, 2) }}</strong>
                </div>

            </div>

            <div class="ticket-actions no-print">
                <button type="button" class="btn-process" onclick="window.print()" style="max-width: 220px;">
                    <span class="btn-label">Imprimir / Guardar PDF</span>
                </button>

                <a href="/ventas" class="venta-quitar" style="margin-left: 16px;">Volver al historial</a>
            </div>

        </main>

    </div>

</body>

</html>