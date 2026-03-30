<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Apartado folio #{{ $apartado->id }}</title>
    <style>
        @page { margin: 22px 24px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #2a2a2a;
            line-height: 1.35;
        }
        h1, h2, h3, p { margin: 0; }

        .doc-header {
            background: #1a1a1a;
            color: #ffffff;
            padding: 14px 16px;
            border-left: 6px solid #D4AF37;
            margin-bottom: 12px;
        }
        .brand {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .doc-title {
            margin-top: 4px;
            font-size: 13px;
            color: #D4AF37;
            font-weight: 700;
        }
        .meta-date {
            margin-top: 4px;
            font-size: 10px;
            color: #d1d1d1;
        }

        .info-box {
            border: 1px solid #d9d9d9;
            margin-bottom: 12px;
        }
        .info-head {
            background: #f5f5f5;
            color: #1f1f1f;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #d9d9d9;
            padding: 6px 8px;
        }
        .info-body {
            padding: 8px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
        }
        .info-label {
            width: 90px;
            color: #6a6a6a;
            font-weight: 700;
        }

        .section {
            margin-top: 10px;
        }
        .section-title {
            background: #2d2d2d;
            color: #D4AF37;
            border-left: 4px solid #D4AF37;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 8px;
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        th, td {
            border: 1px solid #d4d4d4;
            padding: 6px;
            vertical-align: middle;
        }
        th {
            background: #efefef;
            color: #333333;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            text-align: left;
        }
        tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .text-right { text-align: right; }
        .empty {
            border: 1px solid #dcdcdc;
            border-top: none;
            color: #777;
            padding: 8px;
        }

        .summary-box {
            border: 1px solid #d9d9d9;
            margin-top: 12px;
        }
        .summary-head {
            background: #f5f5f5;
            color: #1f1f1f;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #d9d9d9;
            padding: 6px 8px;
        }
        .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            border: none;
            border-bottom: 1px solid #ededed;
            padding: 6px 8px;
            font-size: 11px;
        }
        .totals tr:last-child td {
            border-bottom: none;
            font-weight: 700;
            font-size: 12px;
        }
        .total-label { color: #444; }
        .total-value { text-align: right; }

        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 9px;
            color: #8a8a8a;
        }
    </style>
</head>
<body>
    <div class="doc-header">
        <div class="brand">Joyería Colibrí</div>
        <div class="doc-title">Comprobante de apartado #{{ $apartado->id }}</div>
        <div class="meta-date">Generado: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    <div class="info-box">
        <div class="info-head">Datos del apartado</div>
        <div class="info-body">
            <table class="info-table">
                <tr>
                    <td class="info-label">Folio:</td>
                    <td>#{{ $apartado->id }}</td>
                </tr>
                <tr>
                    <td class="info-label">Fecha:</td>
                    <td>{{ $apartado->created_at ? $apartado->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Cliente:</td>
                    <td>{{ $apartado->cliente->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Vendedor:</td>
                    <td>{{ $apartado->usuario->name ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Productos del apartado</h3>
        @if($apartado->productos && count($apartado->productos) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th class="text-right">Precio</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apartado->productos as $producto)
                        <tr>
                            <td>{{ $producto->codigo_barras }}</td>
                            <td>{{ $producto->descripcion }}</td>
                            <td class="text-right">${{ number_format($producto->precio, 2) }}</td>
                            <td class="text-right">{{ $producto->cantidad }}</td>
                            <td class="text-right">${{ number_format($producto->precio * $producto->cantidad, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay productos registrados en este apartado.</div>
        @endif
    </div>

    <div class="section">
        <h3 class="section-title">Historial de abonos</h3>
        @if($apartado->abonos && count($apartado->abonos) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th class="text-right">Monto</th>
                        <th>Tipo de pago</th>
                        {{-- <th>Observaciones</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($apartado->abonos->sortByDesc('created_at') as $abono)
                        <tr>
                            <td>{{ $abono->created_at ? $abono->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                            <td>{{ $abono->usuario->name ?? 'N/A' }}</td>
                            <td class="text-right">${{ number_format($abono->monto, 2) }}</td>
                            <td>{{ $abono->tipo_pago === 'MERCADO_PAGO' ? 'MÉTODO ELECTRÓNICO' : 'EFECTIVO' }}</td>
                            {{-- <td>{{ $abono->observaciones ?: '-' }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay abonos registrados.</div>
        @endif
    </div>

    <div class="summary-box">
        <div class="summary-head">Resumen</div>
        <table class="totals">
            <tr>
                <td class="total-label">Total del apartado</td>
                <td class="total-value">${{ number_format($apartado->total, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Total abonado</td>
                <td class="total-value">${{ number_format($totalAbonado, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">Saldo pendiente</td>
                <td class="total-value">${{ number_format($saldo, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Joyería Colibrí
        {{-- Documento generado por el sistema de Punto de Venta. --}}
    </div>
</body>
</html>
