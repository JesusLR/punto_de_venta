<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reparación externa #{{ $reparacion->id }}</title>
    @include('pdf.styles')
    <style>
        .repair-description { white-space:pre-line; overflow-wrap:break-word; line-height:1.5; color:#586579; }
        .repair-photos { margin-top:10pt; }
        .repair-photos td { width:50%; text-align:center; padding:8pt; background:#fafbfc; }
        .repair-photos img { max-width:225pt; max-height:180pt; }
        .repair-photos p { color:#7d8794; font-size:6.5pt; margin-top:5pt; }
    </style>
</head>
<body>
    <div class="doc-header">
        <div class="brand">{{ config('app.name') }}</div>
        <div class="doc-title">Comprobante de reparación</div>
        <div class="doc-reference">Reparación externa #{{ $reparacion->id }}</div>
        @if($reparacion->nombre)<div class="doc-reference">{{ $reparacion->nombre }}</div>@endif
        <div class="meta-date">Generado: {{ now()->format('d/m/Y') }}</div>
    </div>
    <div class="info-box"><div class="info-head">Datos de la reparación</div><table class="info-table">
        <tr><td class="info-label">Folio:</td><td>#{{ $reparacion->id }}</td></tr>
        <tr><td class="info-label">Fecha:</td><td>{{ $reparacion->created_at->format('d/m/Y') }}</td></tr>
        <tr><td class="info-label">Cliente:</td><td>{{ $reparacion->contacto_nombre }}</td></tr>
        @if($reparacion->contacto_telefono)<tr><td class="info-label">Teléfono:</td><td>{{ $reparacion->contacto_telefono }}</td></tr>@endif
        <tr><td class="info-label">Estado:</td><td>{{ ['EN_REPARACION' => 'En reparación', 'ENTREGADO' => 'Entregado', 'CANCELADO' => 'Cancelado'][$reparacion->estado] }}</td></tr>
        @if($reparacion->entregado_at)<tr><td class="info-label">Entregado:</td><td>{{ $reparacion->entregado_at->format('d/m/Y') }}</td></tr>@endif
    </table></div>
    <div class="section"><h3 class="section-title">Descripción / Observaciones</h3><div class="repair-description">{{ $reparacion->observaciones }}</div></div>
    <div class="closing-block">
        @if($reparacion->estado === 'ENTREGADO')
            <div class="summary-box"><div class="summary-head">Resumen</div><table class="totals"><tr><td class="total-label">Total de la reparación</td><td class="total-value">${{ number_format($reparacion->precio_venta, 2) }}</td></tr></table></div>
        @endif
    <div class="footer">Gracias por confiar en nosotros.<strong>{{ config('app.name') }}</strong></div>
    </div>
    @if($imagenes->count())
    <div style="page-break-before:always">
        <div class="doc-header"><div class="brand">{{ config('app.name') }}</div><div class="doc-title">Registro fotográfico</div><div class="doc-reference">Reparación externa #{{ $reparacion->id }} · {{ $reparacion->contacto_nombre }}</div></div>
    @foreach(['ANTES' => 'Antes de la reparación', 'DESPUES' => 'Después de la reparación'] as $etapa => $etiqueta)
        @php $fotos = $imagenes->where('etapa', $etapa)->values(); @endphp
        @if($fotos->count())
            <table class="repair-photos"><thead><tr><th colspan="2">{{ $etiqueta }}</th></tr></thead><tbody>
                @foreach($fotos->chunk(2) as $fila)
                    <tr>@foreach($fila as $foto)<td><img src="{{ $foto['ruta'] }}" alt="Imagen de la pieza"><p>{{ $etiqueta }} · Imagen {{ $loop->parent->index * 2 + $loop->iteration }}</p></td>@endforeach @if($fila->count() === 1)<td></td>@endif</tr>
                @endforeach
            </tbody></table>
        @endif
    @endforeach
    </div>
    @endif
</body>
</html>
