@if($apartado->productos && count($apartado->productos) > 0)
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apartado->productos as $prod)
                <tr>
                    <td>{{ $prod->codigo_barras }}</td>
                    <td>{{ $prod->descripcion }}</td>
                    <td>${{ number_format($prod->precio, 2) }}</td>
                    <td>{{ $prod->cantidad }}</td>
                    <td>${{ number_format($prod->precio * $prod->cantidad, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-muted">No hay productos en este apartado.</p>
@endif
