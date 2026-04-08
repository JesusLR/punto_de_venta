<!-- Modal Productos -->
<div class="modal fade apartado-modal" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="apartado-modal-badge">
                        <i class="fas fa-box-open"></i> Inventario
                    </span>
                    <h5 class="modal-title" id="modalProductosLabel"><i class="fas fa-box"></i> Productos del apartado</h5>
                    <p class="apartado-modal-subtitle mb-0">Consulta, agrega y revisa los artículos vinculados a este apartado.</p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body apartado-modal-body-soft" id="productosBody">
                <!-- Se cargará con AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>