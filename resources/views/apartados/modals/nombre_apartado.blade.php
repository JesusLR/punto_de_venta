<!-- Modal Nombre de Apartado -->
<div class="modal fade" id="modalNombreApartado" tabindex="-1" role="dialog" aria-labelledby="modalNombreApartadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNombreApartadoLabel"><i class="fas fa-pen"></i> Editar información del apartado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_apartado_nombre" name="id_apartado">

                <div class="form-group">
                    <label for="nombre-apartado">Nombre del apartado</label>
                    <input type="text" id="nombre-apartado" name="nombre_apartado" class="form-control" maxlength="255" placeholder="Ej. Apartado de anillo" required>
                {{-- </div>

                <div class="form-group mb-0"> --}}
                    <label for="cliente-apartado">Cliente</label>
                    <select class="form-control" id="cliente-apartado" name="id_cliente" required>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btnSaveNombreApartado">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
