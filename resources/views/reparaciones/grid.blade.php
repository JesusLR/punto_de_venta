    <div class="table-container">
        <div class="workspace-table-heading"><div><h2>Historial de reparaciones</h2><p>Consulta las piezas enviadas, su costo y su recepción.</p></div><span>{{ $reparaciones->total() }} registros</span></div>
        <div class="table-responsive">
            <table class="table table-hover apartado-table mb-0 reparaciones-table">
                <thead><tr><th>Reparación / Fecha</th><th>Piezas / Contacto</th><th>Importes</th><th>Estado</th><th>Acciones</th></tr></thead>
                <tbody>
                    @forelse($reparaciones as $reparacion)
                        <tr>
                            <td><strong>{{ $reparacion->nombre ?: 'Reparación #'.$reparacion->id }}</strong><br><span class="badge {{ $reparacion->tipo === 'EXTERNA' ? 'badge-info' : 'badge-secondary' }}">{{ $reparacion->tipo === 'EXTERNA' ? 'Externa' : 'Interna' }}</span><br><small>Folio #{{ $reparacion->id }} · {{ $reparacion->created_at->format('d/m/Y H:i') }}</small><br><small>{{ optional($reparacion->usuario)->name ?: 'Usuario no disponible' }}</small></td>
                            <td>@if($reparacion->tipo === 'EXTERNA'){{ $reparacion->contacto_nombre }}<br><small>{{ $reparacion->contacto_telefono }}</small><br><small>{{ $reparacion->imagenes->count() }} imágenes</small>@else{{ $reparacion->piezas->count() }} productos<br><small>{{ $reparacion->piezas->sum('cantidad') }} unidades</small>@endif</td>
                            <td>@if($reparacion->tipo === 'EXTERNA')@if($reparacion->estado === 'ENTREGADO')<small>Costo: ${{ number_format($reparacion->precio_compra, 2) }}</small><br><strong>Venta: ${{ number_format($reparacion->precio_venta, 2) }}</strong>@else<span class="text-muted">Por definir al entregar</span>@endif @else ${{ number_format($reparacion->total, 2) }} @endif</td>
                            <td>
                                <span class="badge {{ $reparacion->estado === 'ENTREGADO' ? 'badge-success' : ($reparacion->estado === 'CANCELADO' ? 'badge-danger' : 'badge-warning') }}">{{ ['ENTREGADO' => 'Entregado', 'EN_REPARACION' => 'En reparación', 'CANCELADO' => 'Cancelado'][$reparacion->estado] }}</span>
                                @if($reparacion->entregado_at)
                                    <br><small>{{ $reparacion->entregado_at->format('d/m/Y H:i') }}</small><br><small>Egreso #{{ $reparacion->id_egreso }}</small>
                                    @if($reparacion->id_ingreso)<br><small>Ingreso #{{ $reparacion->id_ingreso }}</small>@endif
                                @endif
                                @if($reparacion->cancelado_at)<br><small>{{ $reparacion->cancelado_at->format('d/m/Y H:i') }}</small>@endif
                            </td>
                            <td>
                                <div class="workspace-row-actions">
                                <button type="button" class="btn btn-info" title="Ver piezas" aria-label="Ver piezas" data-toggle="modal" data-target="#detalleReparacion{{ $reparacion->id }}"><i class="fas fa-box" aria-hidden="true"></i></button>
                                @if($reparacion->tipo === 'EXTERNA')<button type="button" class="btn btn-danger btn-ver-pdf-reparacion" data-url="{{ route('reparaciones.pdf', $reparacion) }}" title="Ver PDF" aria-label="Ver PDF"><i class="fas fa-file-pdf" aria-hidden="true"></i></button>@endif
                                @if(Auth::user()->hasPermission('manage_reparaciones'))
                                    <button type="button" class="btn btn-light btn-nombre-reparacion" title="Cambiar nombre" aria-label="Cambiar nombre" data-url="{{ route('reparaciones.nombre', $reparacion) }}" data-nombre="{{ $reparacion->nombre }}"><i class="fas fa-pen" aria-hidden="true"></i></button>
                                    @if($reparacion->tipo === 'EXTERNA')<button type="button" class="btn btn-primary btn-editar-reparacion-externa" data-url="{{ route('reparaciones.externas.edit', $reparacion) }}" title="Editar reparación e imágenes" aria-label="Editar reparación e imágenes"><i class="fas fa-edit" aria-hidden="true"></i></button>@endif
                                @endif
                                @if($reparacion->estado === 'EN_REPARACION' && Auth::user()->hasPermission('manage_reparaciones'))
                                    <button type="button" class="btn btn-success {{ $reparacion->tipo === 'EXTERNA' ? 'btn-entregar-externa' : 'btn-entregar-reparacion' }}" title="Marcar entregado" aria-label="Marcar entregado" data-url="{{ route('reparaciones.entregar', $reparacion) }}" data-folio="{{ $reparacion->id }}" data-total="{{ number_format($reparacion->total, 2) }}"><i class="fas fa-check" aria-hidden="true"></i></button>
                                    <button type="button" class="btn btn-warning btn-cancelar-reparacion" title="Cancelar reparación" aria-label="Cancelar reparación" data-tipo="{{ $reparacion->tipo }}" data-url="{{ route('reparaciones.cancelar', $reparacion) }}" data-folio="{{ $reparacion->id }}"><i class="fas fa-ban" aria-hidden="true"></i></button>
                                @endif
                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No hay reparaciones registradas para este filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $reparaciones->links() }}</div>
    </div>

@foreach($reparaciones as $reparacion)
    @include('reparaciones.modals.piezas')
@endforeach
