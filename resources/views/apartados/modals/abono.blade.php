<!-- Modal Abono -->
<div class="modal" id="modalAbono" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill"></i> Nuevo Abono</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white; font-size: 1.2em;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formAbono" method="post">
                @csrf
                <div class="modal-body" style="padding: 12px;">
                    <input type="hidden" id="id_apartado_abono" name="id_apartado">

                    <!-- Formulario compacto -->
                    <div class="form-group ">
                        <label for="monto_abono">Monto</label>
                        <input type="number" id="monto_abono" class="form-control form-control-sm" name="monto_abono" min="0.01" step="0.01" placeholder="0.00" required >
                        {{-- <small class="form-text text-muted" id="max-abono" style="font-size: 0.7em;"></small> --}}
                    {{-- </div> --}}

                    {{-- <div class="form-group "> --}}
                        <label for="fecha_abono">Fecha del abono</label>
                        <input type="date" id="fecha_abono" class="form-control form-control-sm" name="fecha_abono" required>

                        <label for="tipo_pago_abono">Tipo de pago</label>
                        <select id="tipo_pago_abono" class="form-control form-control-sm" name="tipo_pago" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="MERCADO_PAGO">Mercado Pago</option>
                        </select>

                        <label for="observaciones_abono">Notas</label>
                        <textarea id="observaciones_abono" class="form-control form-control-sm" name="observaciones" rows="1" placeholder="Opcional..." ></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 8px; border-top: 1px solid #ecf0f1;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-success" style="background-color: #27ae60; border: none;">
                        <i class="fas fa-save"></i> Abonar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>