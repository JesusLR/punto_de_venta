<table>
    <tr>
        <td colspan="6">REPORTE DE FINANZAS</td>
    </tr>
    <tr>
        <td colspan="6">Empresa: {{ $appName }}</td>
    </tr>
    <tr>
        <td colspan="6">Rango: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }} | Generado: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="6">RESUMEN FINANCIERO</td>
    </tr>
    <tr>
        <td>Total ingresos</td>
        <td>Total egresos</td>
        <td>Balance neto</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>{{ number_format($totalIngresos, 2, '.', '') }}</td>
        <td>{{ number_format($totalEgresos, 2, '.', '') }}</td>
        <td>{{ number_format($balanceNeto, 2, '.', '') }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th colspan="6">INGRESOS AUTOMÁTICOS</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Referencia</th>
            <th>Método</th>
            <th>Detalle</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($movimientosIngresos as $movimiento)
            <tr>
                <td>{{ $movimiento['fecha']->format('d/m/Y') }}</td>
                <td>{{ $movimiento['tipo'] }}</td>
                <td>{{ $movimiento['referencia'] }}</td>
                <td>{{ str_replace('_', ' ', $movimiento['metodo']) }}</td>
                <td>{{ $movimiento['detalle'] }}</td>
                <td>{{ number_format($movimiento['monto'], 2, '.', '') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Sin ingresos en el rango seleccionado</td>
            </tr>
        @endforelse
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="5">EGRESOS CAPTURADOS</th>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th>Usuario</th>
            <th>Observaciones</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($egresos as $egreso)
            <tr>
                <td>{{ \Carbon\Carbon::parse($egreso->fecha)->format('d/m/Y') }}</td>
                <td>{{ $egreso->concepto }}</td>
                <td>{{ optional($egreso->usuario)->name }}</td>
                <td>{{ $egreso->observaciones }}</td>
                <td>{{ number_format($egreso->monto, 2, '.', '') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Sin egresos en el rango seleccionado</td>
            </tr>
        @endforelse
    </tbody>
</table>
