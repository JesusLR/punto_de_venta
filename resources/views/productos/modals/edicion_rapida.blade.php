<div class="modal fade apartado-modal" id="edicionRapidaModal" tabindex="-1" role="dialog" aria-labelledby="edicionRapidaTitulo" aria-hidden="true"
     data-url="{{ url('/productos') }}" data-imagenes="{{ asset('img/productos') }}">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form class="modal-content" id="formEdicionRapida" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <div>
                    <span class="apartado-modal-badge"><i class="fas fa-bolt"></i> Edición rápida</span>
                    <h5 class="modal-title" id="edicionRapidaTitulo">Actualizar producto</h5>
                    <p class="apartado-modal-subtitle mb-0" id="edicionRapidaDescripcion"></p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="edicionRapidaErrores" role="alert" aria-live="polite"></div>
                <div class="row">
                    <div class="col-md-5 text-center mb-3">
                        <img id="edicionRapidaImagen" alt="Imagen del producto" class="img-fluid rounded mb-3" style="max-height:220px;object-fit:contain" hidden>
                        <p id="edicionRapidaSinImagen" class="text-muted">Sin imagen</p>
                        <div class="form-group text-left">
                            <label for="rapidaImg">Cambiar imagen</label>
                            <input type="file" id="rapidaImg" name="img" class="form-control-file" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="form-text text-muted">JPG, PNG, GIF o WebP. Máximo 2 MB.</small>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group-modern">
                            <label for="rapidaCodigo">Código</label>
                            <input id="rapidaCodigo" name="codigo_barras" class="form-control-modern" type="text" maxlength="255" required>
                        </div>
                        @foreach(['precio_compra' => 'Precio de compra', 'precio_venta' => 'Precio de venta', 'existencia' => 'Existencia'] as $campo => $etiqueta)
                            <div class="form-group-modern">
                                <label for="rapida_{{ $campo }}">{{ $etiqueta }}</label>
                                <input id="rapida_{{ $campo }}" name="{{ $campo }}" class="form-control-modern" type="number" min="0" max="9999999.99" step="0.01" required>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn-modern btn-success-modern" id="guardarEdicionRapida"><i class="fas fa-save"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
