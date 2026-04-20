<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta folio #{{ $venta->id }}</title>
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
        @if (strlen($venta->cNombreVenta) == 0)
            <div class="doc-title">Comprobante de venta folio #{{ $venta->id }}</div>
        @else
            <div class="doc-title">{{ $venta->cNombreVenta }}</div>
        @endif
        <div class="meta-date">Generado: {{ now()->format('d/m/Y ') }}</div>
    </div>

    <div class="info-box">
        <div class="info-head">Datos de la venta</div>
        <div class="info-body">
            <table class="info-table">
                <tr>
                    <td class="info-label">Folio:</td>
                    <td>#{{ $venta->id }}</td>
                </tr>
                <tr>
                    <td class="info-label">Fecha:</td>
                    <td>{{ $venta->created_at ? $venta->created_at->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Cliente:</td>
                    <td>{{ $venta->cliente->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Vendedor:</td>
                    <td>{{ $venta->user->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Pago:</td>
                    <td>
                        @if($venta->tipo_pago === 'MERCADO_PAGO')
                            MERCADO PAGO
                        @elseif($venta->tipo_pago === 'ABONOS')
                            ABONOS
                        @else
                            EFECTIVO
                        @endif
                    </td>
                </tr>
                {{-- <tr>
                    <td class="info-label">Nombre:</td>
                    <td>{{ $venta->cNombreVenta ?: ('Detalle de venta #' . $venta->id) }}</td>
                </tr> --}}
            </table>
        </div>
    </div>

    <div class="section">
        <h3 class="section-title">Productos vendidos</h3>
        @if($venta->productos && count($venta->productos) > 0)
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
                    @foreach($venta->productos as $producto)
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
            <div class="empty">No hay productos registrados en esta venta.</div>
        @endif
    </div>

    <div class="summary-box">
        <div class="summary-head">Resumen</div>
        <table class="totals">
            <tr>
                <td class="total-label">Total de la venta</td>
                <td class="total-value">${{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Joyería Colibrí
    </div>
</body>
</html>
