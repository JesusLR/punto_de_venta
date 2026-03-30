<div class="modal fade" id="editNombreVentaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="tituloEditNombreVentaModal"><i class="fa fa-edit"></i> Editar nombre de la venta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="cNombreProveedor">Nombre de la venta</label>
            <input type="text" class="form-control" id="cNombreVenta" name="cNombreVenta" placeholder="Ingrese un nombre para la venta">
          </div>

          {{-- <div class="form-group">
            <label for="cNotasProveedor">Informacion</label>
            <textarea class="form-control" id="cNotasProveedor" name="cNotasProveedor" rows="3"></textarea>
          </div> --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarNombreVenta">Guardar</button>
        </div>
      </div>
    </div>
  </div>
