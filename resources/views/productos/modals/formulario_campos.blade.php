            <!-- Información Básica -->
            <div class="form-container">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Información Básica
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-barcode"></i>Código de Barras <span class="text-danger">*</span></label>
                            <input required autocomplete="off" maxlength="255" name="codigo_barras" id="productoModalCodigo" 
                                   class="form-control" type="text" placeholder="Ingrese código de barras"
                                   value="{{ old('codigo_barras') }}">
                            <small class="form-text"><i class="fas fa-info-circle"></i>Escanee o ingrese el código manualmente</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i>Descripción <span class="text-danger">*</span></label>
                            <input required autocomplete="off" maxlength="255" name="descripcion" id="productoModalDescripcion"
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
                            <input required autocomplete="off" name="precio_compra" id="productoModal_precio_compra"
                                   class="form-control" type="number" step="0.01" min="0" max="9999999.99" 
                                   placeholder="0.00" value="{{ old('precio_compra') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-cash-register"></i>Precio de Venta <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="precio_venta" id="productoModal_precio_venta"
                                   class="form-control" type="number" step="0.01" min="0" max="9999999.99" 
                                   placeholder="0.00" value="{{ old('precio_venta') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><i class="fas fa-boxes"></i>Existencia <span class="text-danger">*</span></label>
                            <input required autocomplete="off" name="existencia" id="productoModal_existencia"
                                   class="form-control" type="number" step="0.01" min="0" max="9999999.99" 
                                   placeholder="0" value="{{ old('existencia') }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div id="productoModalGanancia" class="profit-indicator" style="display: none;">
                            <i class="fas fa-chart-line"></i>
                            <span>Margen de ganancia: <strong id="productoModalMargen">0%</strong> | Ganancia por unidad: <strong>$<span id="productoModalUtilidad">0.00</span></strong></span>
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
                            <select name="id_proveedor" id="productoModal_id_proveedor" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <option value="0">N/A</option>
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
                            <select name="id_categoria" id="productoModal_id_categoria" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <option value="0">N/A</option>
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
                            <select name="id_material" id="productoModal_id_material" class="form-control" required>
                                <option value="">-- Seleccione --</option>
                                <option value="0">N/A</option>
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
                            <input type="file" class="form-control-file" id="productoModalArchivo" name="img" 
                                   accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="form-text"><i class="fas fa-info-circle"></i>JPG, PNG, GIF o WebP. Máximo 2 MB. Al editar, deja este campo vacío para conservar la imagen.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="preview-container" id="productoModalVistaPrevia">
                            <img id="productoModalImagen" class="preview-image" alt="Imagen del producto" hidden>
                            <div class="preview-placeholder" id="productoModalSinImagen">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Vista previa de la imagen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

