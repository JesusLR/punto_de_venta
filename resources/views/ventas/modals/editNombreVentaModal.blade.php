<div class="modal fade apartado-modal" id="editNombreVentaModal" tabindex="-1" role="dialog" aria-labelledby="tituloEditNombreVentaModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <span class="apartado-modal-badge">
              <i class="fas fa-receipt"></i> Ventas
            </span>
            <h5 class="modal-title" id="tituloEditNombreVentaModal"><i class="fa fa-edit"></i> Editar nombre de la venta</h5>
            <p class="apartado-modal-subtitle mb-0">Actualiza el nombre para identificar la venta más fácilmente en el historial.</p>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="apartado-modal-helper">
            <i class="fas fa-pen-nib"></i>
            <span>Usa un nombre corto y descriptivo para ubicar la venta rápidamente.</span>
          </div>

          <div class="form-group-modern mb-0">
            <label for="cNombreVenta"><i class="fas fa-tag"></i>Nombre de la venta</label>
            <input type="text" class="form-control form-control-modern apartado-modal-input" id="cNombreVenta" name="cNombreVenta" placeholder="Ingrese un nombre para la venta">
            <small class="form-text">
              <i class="fas fa-info-circle"></i>
              Ejemplo: venta de mostrador, pedido especial o anticipo.
            </small>
          </div>

          {{-- <div class="form-group">
            <label for="cNotasProveedor">Informacion</label>
            <textarea class="form-control" id="cNotasProveedor" name="cNotasProveedor" rows="3"></textarea>
          </div> --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="button" class="btn btn-modern btn-success-modern" id="btnGuardarNombreVenta">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
