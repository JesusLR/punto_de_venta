<div class="modal fade apartado-modal reparacion-detalle-modal" id="detalleReparacion{{ $reparacion->id }}" tabindex="-1" role="dialog" aria-labelledby="detalleReparacionTitulo{{ $reparacion->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div><span class="apartado-modal-badge"><i class="fas fa-gem"></i> {{ $reparacion->tipo === 'EXTERNA' ? 'Externa' : 'Interna' }} · Folio #{{ $reparacion->id }}</span><h5 class="modal-title" id="detalleReparacionTitulo{{ $reparacion->id }}">{{ $reparacion->nombre ?: 'Reparación #'.$reparacion->id }}</h5><p class="apartado-modal-subtitle mb-0">@if($reparacion->tipo === 'EXTERNA'){{ ['ENTREGADO' => 'Entregado · Ingreso y egreso registrados', 'CANCELADO' => 'Cancelado · Sin movimientos de Finanzas', 'EN_REPARACION' => 'En reparación · Pendiente de entrega'][$reparacion->estado] }}@else{{ ['ENTREGADO' => 'Entregado · Unidades reintegradas al inventario', 'CANCELADO' => 'Cancelado · Unidades reintegradas sin egreso', 'EN_REPARACION' => 'En reparación · Unidades retiradas de la venta'][$reparacion->estado] }}@endif</p></div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                @if($reparacion->tipo === 'EXTERNA')
                    <div class="reparacion-form-panel"><strong>{{ $reparacion->contacto_nombre }}</strong>@if($reparacion->contacto_telefono)<p class="text-muted mb-0">Teléfono: {{ $reparacion->contacto_telefono }}</p>@endif</div>
                    @if($reparacion->estado === 'ENTREGADO')<div class="reparacion-form-panel mt-3">Costo de reparación: ${{ number_format($reparacion->precio_compra, 2) }}<br>Precio de venta: ${{ number_format($reparacion->precio_venta, 2) }}<br><small>Ingreso #{{ $reparacion->id_ingreso }} · Egreso #{{ $reparacion->id_egreso }}</small></div>@endif
                @else
                <div class="table-responsive"><table class="table reparacion-detalle-table"><thead><tr><th>Código</th><th>Producto</th><th>Unidades</th></tr></thead><tbody>
                    @foreach($reparacion->piezas as $pieza)
                        <tr><td>{{ $pieza->codigo_barras }}</td><td>{{ $pieza->descripcion }}</td><td>{{ $pieza->cantidad }}</td></tr>
                    @endforeach
                </tbody></table></div>
                <div class="reparacion-form-panel mt-3"><strong>Total reparación: ${{ number_format($reparacion->total, 2) }}</strong><p class="text-muted mb-0">{{ $reparacion->piezas->sum('cantidad') }} unidades en {{ $reparacion->piezas->count() }} productos</p></div>
                @endif
                @if($reparacion->observaciones)<p class="mt-3 mb-1" style="white-space:pre-line">{{ $reparacion->observaciones }}</p>@endif
                @foreach(['ANTES' => 'Antes de la reparación', 'DESPUES' => 'Después de la reparación'] as $etapa => $etiqueta)
                    @if($reparacion->imagenes->where('etapa', $etapa)->count())<h6 class="workspace-section-heading mt-4">{{ $etiqueta }}</h6><div class="reparacion-imagenes">@foreach($reparacion->imagenes->where('etapa', $etapa) as $imagen)<button type="button" class="btn-ver-imagen-reparacion" data-url="{{ route('reparaciones.imagen', [$reparacion, $imagen]) }}" aria-label="Ampliar imagen {{ $loop->iteration }}"><img src="{{ route('reparaciones.imagen', [$reparacion, $imagen]) }}" alt="{{ $etiqueta }} · Imagen {{ $loop->iteration }}" loading="lazy"></button>@endforeach</div>@endif
                @endforeach
                @if($reparacion->entregado_at)<p class="text-muted mt-3 mb-0">Recibido por {{ optional($reparacion->receptor)->name ?: 'Usuario no disponible' }} el {{ $reparacion->entregado_at->format('d/m/Y H:i') }}. Egreso #{{ $reparacion->id_egreso }}.</p>@endif
                @if($reparacion->cancelado_at)<p class="text-muted mt-3 mb-0">Cancelado por {{ optional($reparacion->cancelador)->name ?: 'Usuario no disponible' }} el {{ $reparacion->cancelado_at->format('d/m/Y H:i') }}.</p>@endif
            </div>
            <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>
