<div class="modal fade" id="formMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloFormMateriales"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="cNombreMaterial">Nombre</label>
            <input type="text" class="form-control" id="cNombreMaterial" name="cNombreMaterial" placeholder="Ingrese el nombre del material">
          </div>

          <div class="form-group">
            <label for="cNotasMaterial">Informacion</label>
            <textarea class="form-control" id="cNotasMaterial" name="cNotasMaterial" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">cerrar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarMaterial">Guardar</button>
        </div>
      </div>
    </div>
  </div>
