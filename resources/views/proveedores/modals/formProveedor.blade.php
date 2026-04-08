<div class="modal fade apartado-modal" id="formProveedorModal" tabindex="-1" role="dialog" aria-labelledby="tituloFormProveedores" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <span class="apartado-modal-badge">
              <i class="fas fa-truck"></i> Proveedores
            </span>
            <h5 class="modal-title" id="tituloFormProveedores"><i class="fas fa-edit"></i> Gestión de proveedor</h5>
            <p class="apartado-modal-subtitle mb-0">Crea o edita un proveedor para tener mejor control de compras y abastecimiento.</p>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="apartado-modal-helper">
            <i class="fas fa-handshake"></i>
            <span>Registra un nombre claro y una nota breve para identificar fácilmente a cada proveedor.</span>
          </div>

          <div class="form-group-modern">
            <label for="cNombreProveedor"><i class="fas fa-tag"></i>Nombre</label>
            <input type="text" class="form-control form-control-modern apartado-modal-input" id="cNombreProveedor" name="cNombreProveedor" placeholder="Ingrese el nombre del proveedor">
          </div>

          <div class="form-group-modern mb-0">
            <label for="cNotasProveedor"><i class="fas fa-sticky-note"></i>Información</label>
            <textarea class="form-control form-control-modern" id="cNotasProveedor" name="cNotasProveedor" rows="3" placeholder="Describe brevemente este proveedor"></textarea>
            <small class="form-text">
              <i class="fas fa-info-circle"></i>
              Puedes guardar datos útiles como especialidad, condiciones o referencias.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="button" class="btn btn-modern btn-success-modern" id="btnGuardarProveedor">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
