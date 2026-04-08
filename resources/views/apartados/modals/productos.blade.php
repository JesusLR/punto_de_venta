<div class="mb-4 p-3 border rounded bg-light">
    <h6 class="mb-3">Agregar producto al apartado</h6>
    <input type="hidden" id="id_apartado_producto" value="{{ $apartado->id }}">
    <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="id_producto_apartado" class="font-weight-bold">Producto</label>
            <select id="id_producto_apartado" class="form-control">
                <option value="">-- Selecciona un producto --</option>
                @foreach($productosDisponibles as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->codigo_barras }} - {{ $producto->descripcion }} (Existencia: {{ $producto->existencia }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="cantidad_producto_apartado" class="font-weight-bold">Cantidad</label>
            <input type="number" id="cantidad_producto_apartado" class="form-control" min="1" step="1" value="1">
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-primary w-100" onclick="agregarProductoAApartado()">
                <i class="fas fa-plus"></i> 
            </button>
        </div>
    </div>
</div> 


@if($apartado->productos && count($apartado->productos) > 0)
    <h6 class="mb-3 mt-4">Productos en el apartado</h6>
    <table class="table table-sm table-bordered table-hover">
        <thead class="table-light">
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
    <p class="text-muted mt-4">No hay productos en este apartado.</p>
@endif
