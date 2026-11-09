<div class="modal fade apartado-modal" id="productoFormularioModal" tabindex="-1" role="dialog" aria-labelledby="productoFormularioTitulo" aria-hidden="true"
     data-create-url="{{ route('productos.modal.store') }}" data-base-url="{{ url('/productos') }}" data-imagenes="{{ asset('img/productos') }}">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
        <form class="modal-content" id="formProductoModal" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <div>
                    <span class="apartado-modal-badge"><i class="fas fa-gem"></i> Productos</span>
                    <h5 class="modal-title" id="productoFormularioTitulo">Nuevo producto</h5>
                    <p class="apartado-modal-subtitle mb-0">Información, precios e inventario de la pieza.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="productoFormularioErrores" role="alert" aria-live="polite"></div>
                @include('productos.modals.formulario_campos')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn-modern btn-success-modern" id="guardarProductoModal"><i class="fas fa-save"></i> Guardar producto</button>
            </div>
        </form>
    </div>
</div>
