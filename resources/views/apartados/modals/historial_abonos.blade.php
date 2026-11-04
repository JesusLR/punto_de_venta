@if(request()->routeIs('apartados.verAbonos'))
    <div class="workspace-history-context" hidden>
        <span class="workspace-eyebrow">Historial del apartado #{{ $apartado->id }}</span>
        <strong>{{ $apartado->nombre_apartado ?: 'Detalle de pagos' }}</strong>
    </div>
    <div class="workspace-payment-summary" hidden>
        <div><span>Total del apartado</span><strong>${{ number_format((float) $apartado->total, 2) }}</strong></div>
        <div><span>Abonado · {{ $abonos->count() }} movimientos</span><strong>${{ number_format((float) $abonos->sum('monto'), 2) }}</strong></div>
        <div class="is-pending"><span>Saldo pendiente</span><strong>${{ number_format((float) $apartado->total - (float) $abonos->sum('monto'), 2) }}</strong></div>
    </div>
@endif
@if($abonos && count($abonos) > 0)
    <div class="table-responsive apartado-table-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <table class="table table-sm table-bordered apartado-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Monto</th>
                <th>Tipo de pago</th>
                <th>Observaciones</th>
                @if (Auth::user()->hasPermission('manage_apartados'))
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($abonos as $abono)
                <tr>
                    <td>{{ $abono->fecha_registro ? $abono->fecha_registro->format('d/m/Y') : '-' }}</td>
                    <td>{{ $abono->usuario->name ?? 'N/A' }}</td>
                    <td>${{ number_format($abono->monto, 2) }}</td>
                    <td>{!! $abono->tipo_pago === 'MERCADO_PAGO' ? '<span class="badge badge-warning">MERCADO PAGO</span>' : '<span class="badge badge-info">EFECTIVO</span>' !!}</td>
                    <td>{{ $abono->observaciones ?: '-' }}</td>
                    @if (Auth::user()->hasPermission('manage_apartados'))
                        <td>
                                <button type="button" class="btn btn-sm btn-primary apartado-action-btn" title="Editar fecha"
                                        onclick="editarFechaAbono({{ $abono->id }}, {{ $apartado->id }}, '{{ optional($abono->fecha_registro)->format('Y-m-d') }}')">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger apartado-action-btn" title="Eliminar abono" aria-label="Eliminar abono" onclick="eliminarAbono({{ $abono->id }}, {{ $apartado->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@else
    <div class="apartado-modal-empty">
        <i class="fas fa-receipt"></i>
        <span>No hay abonos registrados en este apartado.</span>
    </div>
@endif
