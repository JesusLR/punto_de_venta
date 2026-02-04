{{-- moved modal styles to public/css/productos-styles.css --}}
<div class="modal fade bd-example-modal-lg" id="imagenProductoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloImagenModal"><i class="fas fa-image" style="margin-right: 0.8rem; color: #D4AF37;"></i></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="divImagenProductoModal">
                {{-- imagen inyectada dinámicamente --}}
            </div>
        </div>
        <div class="modal-footer">
          {{-- botón opcional --}}
      </div>
    </div>
  </div>
