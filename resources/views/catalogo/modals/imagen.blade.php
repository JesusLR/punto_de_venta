<style>
    #divimagenProductoCatalogoModal {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    #divimagenProductoCatalogoModal img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Ajusta la imagen sin deformarla */
    }

</style>

<div class="modal fade bd-example-modal-lg" id="imagenProductoCatalogoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloImagenModal"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="divimagenProductoCatalogoModal">

            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">cerrar</button>
          {{-- <button type="button" class="btn btn-primary" id="btnGuardarInfoExcell">Guardar</button> --}}
        </div>
      </div>
    </div>
  </div>
