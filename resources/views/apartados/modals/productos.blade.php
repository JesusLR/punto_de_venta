<div class="apartado-inline-card mb-4">
    <div class="apartado-inline-card-title mb-3">
        <i class="fas fa-plus-circle"></i>
        Agregar producto al apartado
    </div>
    <input type="hidden" id="id_apartado_producto" value="{{ $apartado->id }}">
    <div class="form-row">
        <div class="col-md-6 mb-3">
            <label for="id_producto_apartado" class="font-weight-bold">Producto</label>
            <select id="id_producto_apartado" class="form-control form-control-modern apartado-modal-input">
                <option value="">-- Selecciona un producto --</option>
                @foreach($productosDisponibles as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->codigo_barras }} - {{ $producto->descripcion }} (Existencia: {{ $producto->existencia }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label for="cantidad_producto_apartado" class="font-weight-bold">Cantidad</label>
            <input type="number" id="cantidad_producto_apartado" class="form-control form-control-modern apartado-modal-input" min="1" step="1" value="1">
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-modern btn-success-modern w-100 justify-content-center" onclick="agregarProductoAApartado()">
                <i class="fas fa-plus"></i> Agregar
            </button>
        </div>
    </div>
</div> 


@if($apartado->productos && count($apartado->productos) > 0)
    <div class="apartado-inline-card-title mt-4 mb-3">
        <i class="fas fa-list"></i>
        Productos en el apartado
    </div>
    <div class="table-responsive apartado-table-wrapper">
    <table class="table table-sm table-bordered table-hover apartado-table">
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
    </div>
@else
    <div class="apartado-modal-empty mt-4">
        <i class="fas fa-box-open"></i>
        <span>No hay productos en este apartado.</span>
    </div>
@endif
