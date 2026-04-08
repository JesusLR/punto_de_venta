<div class="modal fade apartado-modal" id="formCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="tituloFormCategorias" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <span class="apartado-modal-badge">
              <i class="fas fa-tags"></i> Categorías
            </span>
            <h5 class="modal-title" id="tituloFormCategorias"><i class="fas fa-edit"></i> Gestión de categoría</h5>
            <p class="apartado-modal-subtitle mb-0">Crea o edita una categoría para mantener mejor organizado tu catálogo.</p>
          </div>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="apartado-modal-helper">
            <i class="fas fa-folder-open"></i>
            <span>Usa nombres claros y una descripción breve para ubicar mejor los productos dentro del sistema.</span>
          </div>

          <div class="form-group-modern">
            <label for="cNombreCategoria"><i class="fas fa-tag"></i>Nombre</label>
            <input type="text" class="form-control form-control-modern apartado-modal-input" id="cNombreCategoria" name="cNombreCategoria" placeholder="Ingrese el nombre de la categoría">
          </div>

          <div class="form-group-modern mb-0">
            <label for="cNotasCategoria"><i class="fas fa-sticky-note"></i>Información</label>
            <textarea class="form-control form-control-modern" id="cNotasCategoria" name="cNotasCategoria" rows="3" placeholder="Describe brevemente esta categoría"></textarea>
            <small class="form-text">
              <i class="fas fa-info-circle"></i>
              Esta información puede ayudarte a distinguir categorías similares.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="button" class="btn btn-modern btn-success-modern" id="btnGuardarCategoria">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
