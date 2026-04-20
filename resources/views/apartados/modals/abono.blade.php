<!-- Modal Abono -->
<div class="modal fade apartado-modal" id="modalAbono" tabindex="-1" role="dialog" aria-labelledby="modalAbonoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <span class="apartado-modal-badge">
                        <i class="fas fa-coins"></i> Abonos
                    </span>
                    <h5 class="modal-title" id="modalAbonoLabel"><i class="fas fa-money-bill-wave"></i> Nuevo abono</h5>
                    <p class="apartado-modal-subtitle mb-0">Registra el pago y deja evidencia del movimiento en el historial.</p>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formAbono" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="id_apartado_abono" name="id_apartado">

                    <div class="apartado-modal-helper">
                        <i class="fas fa-shield-alt"></i>
                        <span>Captura el monto exacto y el tipo de pago para mantener el control del apartado.</span>
                    </div>

                    <div class="form-group-modern">
                        <label for="monto_abono"><i class="fas fa-dollar-sign"></i>Monto</label>
                        <input type="text" id="monto_abono" class="form-control form-control-modern apartado-modal-input" name="monto_abono" inputmode="decimal" autocomplete="off" placeholder="0.00" required>
                    </div>

                    <div class="form-group-modern">
                        <label for="fecha_abono"><i class="fas fa-calendar-alt"></i>Fecha del abono</label>
                        <input type="date" id="fecha_abono" class="form-control form-control-modern apartado-modal-input" name="fecha_abono" required>
                    </div>

                    <div class="form-group-modern">
                        <label for="tipo_pago_abono"><i class="fas fa-wallet"></i>Tipo de pago</label>
                        <select id="tipo_pago_abono" class="form-control form-control-modern apartado-modal-input" name="tipo_pago" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="MERCADO_PAGO">Mercado Pago</option>
                        </select>
                    </div>

                    <div class="form-group-modern mb-0">
                        <label for="observaciones_abono"><i class="fas fa-sticky-note"></i>Notas</label>
                        <textarea id="observaciones_abono" class="form-control form-control-modern" name="observaciones" rows="2" placeholder="Opcional..."></textarea>
                        <small class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Puedes agregar una referencia breve sobre el pago.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-modern btn-success-modern" id="btnSaveAbonoApartado">
                        <i class="fas fa-save"></i> Abonar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>