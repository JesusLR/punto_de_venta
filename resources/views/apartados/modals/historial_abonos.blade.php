@if($abonos && count($abonos) > 0)
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Monto</th>
                <th>Tipo de pago</th>
                <th>Observaciones</th>
                @if (Auth::user()->id == 1)
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
                    @if (Auth::user()->id == 1)
                        <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarAbono({{ $abono->id }}, {{ $apartado->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted">No hay abonos registrados en este apartado.</p>
@endif
