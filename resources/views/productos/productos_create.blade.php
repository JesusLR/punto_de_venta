{{-- filepath: c:\laragon\www\punto_de_venta\resources\views\productos\productos_create.blade.php --}}
@extends("maestra")
@section("titulo", "Agregar producto")
@section("contenido")

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <h1>
                <i class="fas fa-plus-circle"></i>
                Agregar Nuevo Producto
            </h1>
        </div>

        @include("notificacion")

        <form method="POST" action="{{route("productos.store")}}" enctype="multipart/form-data" id="formProducto">
            @csrf
            
            <!-- Información Básica -->
            <div class="form-container">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Información Básica
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-barcode"></i>Código de Barras <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="codigo_barras" id="codigo_barras" 
                                   class="form-control" type="text" placeholder="Ingrese código de barras"
                                   value="{{ old('codigo_barras') }}">
                            <small class="form-text"><i class="fas fa-info-circle"></i>Escanee o ingrese el código manualmente</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i>Descripción <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="descripcion" id="descripcion"
                                   class="form-control" type="text" placeholder="Nombre del producto"
                                   value="{{ old('descripcion') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Precios e Inventario -->
            <div class="form-container">
                <div class="section-title">
                    <i class="fas fa-dollar-sign"></i> Precios e Inventario
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-shopping-cart"></i>Precio de Compra <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="precio_compra" id="precio_compra"
                                   class="form-control" type="number" step="0.01" min="0" 
                                   placeholder="0.00" value="{{ old('precio_compra') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-cash-register"></i>Precio de Venta <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="precio_venta" id="precio_venta"
                                   class="form-control" type="number" step="0.01" min="0" 
                                   placeholder="0.00" value="{{ old('precio_venta') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-boxes"></i>Existencia <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="existencia" id="existencia"
                                   class="form-control" type="number" step="0.01" min="0" 
                                   placeholder="0" value="{{ old('existencia') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="profit-info" class="profit-indicator" style="display: none;">
                            <i class="fas fa-chart-line"></i>
                            <span>Margen de ganancia: <strong id="profit-margin">0%</strong> | Ganancia por unidad: <strong>$<span id="profit-amount">0.00</span></strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clasificación -->
            <div class="form-container">
                <div class="section-title">
                    <i class="fas fa-layer-group"></i> Clasificación
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-truck"></i>Proveedor <span class="text-danger">*</span></label>
                            <select name="id_proveedor" id="id_proveedor" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($lstProveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" 
                                            {{ old('id_proveedor') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->cNombreProveedor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-folder"></i>Categoría <span class="text-danger">*</span></label>
                            <select name="id_categoria" id="id_categoria" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($lstCategorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                            {{ old('id_categoria') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->cNombreCategoria }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-gem"></i>Material <span class="text-danger">*</span></label>
                            <select name="id_material" id="id_material" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($lstMateriales as $material)
                                    <option value="{{ $material->id }}"
                                            {{ old('id_material') == $material->id ? 'selected' : '' }}>
                                        {{ $material->cNombreMaterial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imagen del Producto -->
            <div class="form-container">
                <div class="section-title">
                    <i class="fas fa-image"></i> Imagen del Producto
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-file-image"></i>Seleccionar Imagen</label>
                            <input type="file" class="form-control-file" id="img" name="img" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="form-text"><i class="fas fa-info-circle"></i>Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="preview-container" id="preview-container">
                            <div class="preview-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Vista previa de la imagen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-container">
                <button type="submit" class="btn-action">
                    <i class="fas fa-save"></i> Guardar Producto
                </button>
                <a class="btn-action btn-secondary" href="{{route("productos.index")}}">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    var url_logo = "{{ url('/img/productos/') }}";

    document.addEventListener('DOMContentLoaded', function() {
        // Vista previa de imagen
        const imgInput = document.getElementById('img');
        const previewContainer = document.getElementById('preview-container');

        imgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'La imagen debe ser menor a 2MB',
                        icon: 'warning',
                        confirmButtonColor: '#D4AF37'
                    });
                    imgInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });

        // Cálculo de margen de ganancia
        const precioCompra = document.getElementById('precio_compra');
        const precioVenta = document.getElementById('precio_venta');
        const profitInfo = document.getElementById('profit-info');
        const profitMargin = document.getElementById('profit-margin');
        const profitAmount = document.getElementById('profit-amount');

        function calcularGanancia() {
            const compra = parseFloat(precioCompra.value) || 0;
            const venta = parseFloat(precioVenta.value) || 0;

            if (compra > 0 && venta > 0) {
                const ganancia = venta - compra;
                const margen = ((ganancia / compra) * 100).toFixed(2);
                
                profitMargin.textContent = margen + '%';
                profitAmount.textContent = ganancia.toFixed(2);
                profitInfo.style.display = 'flex';
                
                if (ganancia > 0) {
                    profitInfo.className = 'profit-indicator profit-positive';
                } else {
                    profitInfo.className = 'profit-indicator profit-negative';
                }
            } else {
                profitInfo.style.display = 'none';
            }
        }

        precioCompra.addEventListener('input', calcularGanancia);
        precioVenta.addEventListener('input', calcularGanancia);

        // Validación del formulario
        document.getElementById('formProducto').addEventListener('submit', function(e) {
            const compra = parseFloat(precioCompra.value) || 0;
            const venta = parseFloat(precioVenta.value) || 0;

            if (venta < compra) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Confirmar?',
                    text: 'El precio de venta es menor al precio de compra',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D4AF37',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formProducto').submit();
                    }
                });
            }
        });
    });
</script>

@endsection