<div class="modal fade apartado-modal" id="nombreReparacionModal" tabindex="-1" role="dialog" aria-labelledby="nombreReparacionTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="formNombreReparacion">
            @csrf
            <div class="modal-header"><div><span class="apartado-modal-badge"><i class="fas fa-pen"></i> Reparaciones</span><h5 class="modal-title" id="nombreReparacionTitulo">Cambiar nombre</h5></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="nombreReparacionErrores" role="alert" aria-live="polite"></div>
                <div class="form-group-modern mb-0"><label for="nombreReparacionInput">Nombre</label><input name="nombre" id="nombreReparacionInput" class="form-control" maxlength="150" autocomplete="off"><small class="form-text text-muted">Déjalo vacío para mostrar el folio como nombre.</small></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn-modern btn-success-modern">Guardar nombre</button></div>
        </form>
    </div>
</div>
