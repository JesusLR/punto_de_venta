<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta folio #{{ $venta->id }}</title>
    @include('pdf.styles')
</head>
<body>
    <div class="doc-header">
        <div class="brand">{{ config('app.name') }}</div>
        <div class="doc-title">Comprobante de pago</div>
        <div class="doc-reference">Venta #{{ $venta->id }}</div>
        @if (!empty($venta->cNombreVenta))
            <div class="doc-reference">{{ $venta->cNombreVenta }}</div>
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
            <table class="product-table">
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

    <div class="closing-block">
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
        Gracias por confiar en nosotros.
        <strong>{{ config('app.name') }}</strong>
    </div>
    </div>
</body>
</html>
