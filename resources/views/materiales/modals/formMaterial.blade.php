<div class="modal fade apartado-modal" id="formMaterialModal" tabindex="-1" role="dialog" aria-labelledby="tituloFormMateriales" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <span class="apartado-modal-badge">
              <i class="fas fa-hammer"></i> Materiales
            </span>
            <h5 class="modal-title" id="tituloFormMateriales"><i class="fas fa-edit"></i> Gestión de material</h5>
            <p class="apartado-modal-subtitle mb-0">Crea o edita un material para clasificar mejor tus productos y precios.</p>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="apartado-modal-helper">
            <i class="fas fa-gem"></i>
            <span>Usa nombres claros y agrega una descripción breve para distinguir fácilmente cada material.</span>
          </div>

          <div class="form-group-modern">
            <label for="cNombreMaterial"><i class="fas fa-tag"></i>Nombre</label>
            <input type="text" class="form-control form-control-modern apartado-modal-input" id="cNombreMaterial" name="cNombreMaterial" placeholder="Ingrese el nombre del material">
          </div>

          <div class="form-group-modern mb-0">
            <label for="cNotasMaterial"><i class="fas fa-sticky-note"></i>Información</label>
            <textarea class="form-control form-control-modern" id="cNotasMaterial" name="cNotasMaterial" rows="3" placeholder="Describe brevemente este material"></textarea>
            <small class="form-text">
              <i class="fas fa-info-circle"></i>
              Esta información puede ayudarte a identificar usos o características especiales.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="button" class="btn btn-modern btn-success-modern" id="btnGuardarMaterial">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
