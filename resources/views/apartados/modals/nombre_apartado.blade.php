<!-- Modal Nombre de Apartado -->
<div class="modal fade apartado-modal" id="modalNombreApartado" tabindex="-1" role="dialog" aria-labelledby="modalNombreApartadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="apartado-modal-badge">
                        <i class="fas fa-clipboard-list"></i> Apartado
                    </span>
                    <h5 class="modal-title" id="modalNombreApartadoLabel">
                        <i class="fas fa-pen"></i> Editar información del apartado
                    </h5>
                    <p class="apartado-modal-subtitle mb-0">Actualiza el nombre y el cliente asignado en un solo lugar.</p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_apartado_nombre" name="id_apartado">

                <div class="apartado-modal-helper">
                    <i class="fas fa-lightbulb"></i>
                    <span>Usa un nombre claro para identificar el apartado rápidamente en la tabla.</span>
                </div>

                <div class="form-group-modern">
                    <label for="nombre-apartado"><i class="fas fa-tag"></i>Nombre del apartado</label>
                    <input type="text" id="nombre-apartado" name="nombre_apartado" class="form-control form-control-modern apartado-modal-input" maxlength="255" placeholder="Ej. Apartado de anillo" required>
                    <small class="form-text">
                        <i class="fas fa-info-circle"></i>
                        Agrega una descripción corta y fácil de reconocer.
                    </small>
                </div>

                <div class="form-group-modern mb-0">
                    <label for="cliente-apartado"><i class="fas fa-user"></i>Cliente asignado</label>
                    <select class="form-control form-control-modern apartado-modal-input" id="cliente-apartado" name="id_cliente" required>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                    <small class="form-text">
                        <i class="fas fa-user-check"></i>
                        Selecciona a la persona responsable de este apartado.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-modern btn-success-modern" id="btnSaveNombreApartado">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
