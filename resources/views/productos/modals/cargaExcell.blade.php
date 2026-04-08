<div class="modal fade apartado-modal" id="cargaExcellModal" tabindex="-1" role="dialog" aria-labelledby="cargaExcellModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <span class="apartado-modal-badge">
              <i class="fas fa-file-excel"></i> Importación
            </span>
            <h5 class="modal-title" id="cargaExcellModalLabel"><i class="fas fa-upload"></i> Cargar Excel de productos</h5>
            <p class="apartado-modal-subtitle mb-0">Importa productos desde un archivo Excel para agilizar la carga de inventario.</p>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="apartado-modal-helper">
              <i class="fas fa-table"></i>
              <span>Selecciona un archivo válido en formato .xlsx o .xls para comenzar la importación.</span>
            </div>

            <div class="form-group-modern mb-0">
              <label for="fileExcellProductos"><i class="fas fa-file-upload"></i>Archivo de Excel</label>
              <input type="file" id="fileExcellProductos" class="form-control form-control-modern apartado-modal-input" accept=".xlsx, .xls">
              <small class="form-text">
                <i class="fas fa-info-circle"></i>
                Formatos aceptados: .xlsx y .xls.
              </small>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="button" class="btn btn-modern btn-success-modern" id="btnGuardarInfoExcell">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
