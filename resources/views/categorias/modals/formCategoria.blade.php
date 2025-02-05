<div class="modal fade" id="formCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloFormCategorias"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="cNombreCategoria">Nombre</label>
            <input type="text" class="form-control" id="cNombreCategoria" name="cNombreCategoria" placeholder="Ingrese el nombre de la Categoria">
          </div>

          <div class="form-group">
            <label for="cNotasCategoria">Informacion</label>
            <textarea class="form-control" id="cNotasCategoria" name="cNotasCategoria" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">cerrar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarCategoria">Guardar</button>
        </div>
      </div>
    </div>
  </div>
