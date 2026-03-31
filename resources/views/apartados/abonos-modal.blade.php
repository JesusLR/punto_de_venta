@if($abonos && count($abonos) > 0)
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Monto</th>
                <th>Tipo de pago</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($abonos as $abono)
                <tr>
                    <td>{{ $abono->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $abono->usuario->name ?? 'N/A' }}</td>
                    <td>${{ number_format($abono->monto, 2) }}</td>
                    <td>{{ $abono->tipo_pago === 'MERCADO_PAGO' ? 'MERCADO PAGO' : 'EFECTIVO' }}</td>
                    <td>{{ $abono->observaciones ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted">No hay abonos registrados en este apartado.</p>
@endif
