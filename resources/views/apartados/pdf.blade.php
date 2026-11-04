<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Apartado folio #{{ $apartado->id }}</title>
    @include('pdf.styles')
</head>
<body>
    <div class="doc-header">
        <div class="brand">{{ config('app.name') }}</div>
        <div class="doc-title">Historial de abonos</div>
        <div class="doc-reference">Apartado #{{ $apartado->id }}</div>
        @if (!empty($apartado->nombre_apartado))
            <div class="doc-reference">{{ $apartado->nombre_apartado }}</div>
        @endif
        
        <div class="meta-date">Generado: {{ now()->format('d/m/Y') }}</div>
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
                    <td>{{ $apartado->created_at ? $apartado->created_at->format('d/m/Y') : '-' }}</td>
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
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        {{-- <th>Usuario</th> --}}
                        <th>Tipo de pago</th>
                        <th class="text-right">Monto</th>
                        {{-- <th>Observaciones</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($apartado->abonos->sortByDesc(function ($abono) { return $abono->fecha_registro; }) as $abono)
                        <tr>
                            <td>{{ $abono->fecha_registro ? $abono->fecha_registro->format('d/m/Y') : '-' }}</td>
                            {{-- <td>{{ $abono->usuario->name ?? 'N/A' }}</td> --}}
                            <td>{{ $abono->tipo_pago === 'MERCADO_PAGO' ? 'MÉTODO ELECTRÓNICO' : 'EFECTIVO' }}</td>
                            <td class="text-right">${{ number_format($abono->monto, 2) }}</td>
                            {{-- <td>{{ $abono->observaciones ?: '-' }}</td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay abonos registrados.</div>
        @endif
    </div>

    <div class="closing-block">
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
        Gracias por confiar en nosotros.
        <strong>{{ config('app.name') }}</strong>
        {{-- Documento generado por el sistema de Punto de Venta. --}}
    </div>
    </div>
</body>
</html>
