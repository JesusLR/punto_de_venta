<div class="modal fade apartado-modal reparacion-externa-modal" id="nuevaReparacionExternaModal" tabindex="-1" role="dialog" aria-labelledby="reparacionExternaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
        <form class="modal-content" id="formReparacionExterna" enctype="multipart/form-data" data-url="{{ route('reparaciones.externas.store') }}" data-clientes="{{ route('reparaciones.clientes') }}">
            @csrf
            <input name="solicitud_id" type="hidden" value="{{ \Illuminate\Support\Str::uuid() }}">
            <div class="modal-header"><div><span class="apartado-modal-badge"><i class="fas fa-user"></i> Externa</span><h5 class="modal-title" id="reparacionExternaTitulo">Nueva reparación externa</h5><p class="apartado-modal-subtitle mb-0">Registra quién entrega la pieza, el trabajo solicitado y sus fotografías.</p></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="reparacionExternaErrores" role="alert" aria-live="polite"></div>
                <div class="reparacion-piezas-layout">
                    <section class="reparacion-form-panel">
                        <h6 class="workspace-section-heading"><i class="fas fa-user"></i> Contacto</h6>
                        <div id="externaContactoResumen" hidden></div>
                        <div id="externaContactoCaptura">
                        <div class="form-group-modern"><label for="externaOrigen">Cliente</label><select name="contacto_origen" id="externaOrigen" class="form-control"><option value="catalogo">Seleccionar del catálogo de clientes</option><option value="manual">Capturar nombre y teléfono</option></select></div>
                        <div id="externaContactoCatalogo">
                            <div class="form-group-modern"><label for="externaBuscarCliente">Buscar cliente</label><input id="externaBuscarCliente" class="form-control" placeholder="Nombre del cliente" maxlength="255" autocomplete="off"></div>
                            <div class="form-group-modern"><label for="externaCliente">Cliente seleccionado</label><select id="externaCliente" name="contacto_cliente_id" class="form-control" required><option value="">Selecciona un cliente</option></select></div>
                        </div>
                        <div class="form-group-modern" id="externaContactoManual" hidden><label for="externaNombre">Nombre</label><input name="contacto_nombre" id="externaNombre" class="form-control" maxlength="255" disabled></div>
                        <div class="form-group-modern mb-0"><label for="externaTelefono">Teléfono <span id="externaTelefonoOpcional">(opcional)</span></label><input name="contacto_telefono" id="externaTelefono" class="form-control" type="tel" maxlength="25" placeholder="Número de contacto"></div>
                        </div>
                    </section>
                    <section class="reparacion-form-panel">
                        <h6 class="workspace-section-heading"><i class="fas fa-clipboard-list"></i> Trabajo solicitado</h6>
                        <div class="form-group-modern"><label for="externaNombreReparacion">Nombre de la reparación (opcional)</label><input name="nombre" id="externaNombreReparacion" class="form-control" maxlength="150"></div>
                        <div class="form-group-modern mb-0"><label for="externaObservaciones">Descripción / Observaciones</label><textarea id="externaObservaciones" name="observaciones" class="form-control" rows="7" maxlength="5000" required placeholder="Describe la pieza, su estado y la reparación solicitada"></textarea></div>
                    </section>
                </div>
                <section class="reparacion-form-panel mt-3">
                    <h6 class="workspace-section-heading"><i class="fas fa-camera"></i> Imágenes antes y después</h6>
                    <div class="row">
                        @foreach(['ANTES' => 'Antes de la reparación', 'DESPUES' => 'Después de la reparación'] as $etapa => $etiqueta)
                            <div class="col-lg-6 mb-3">
                                <h6>{{ $etiqueta }}</h6>
                                <div id="externaExistentes{{ $etapa }}" class="reparacion-imagenes mb-3"></div>
                                <label for="externaImagenes{{ $etapa }}">Agregar imágenes</label><input id="externaImagenes{{ $etapa }}" name="imagenes_{{ strtolower($etapa) }}[]" class="form-control-file externa-imagenes" data-etapa="{{ $etapa }}" type="file" multiple accept="image/jpeg,image/png">
                                <div id="externaVistaImagenes{{ $etapa }}" class="reparacion-imagenes mt-3"></div>
                            </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Hasta 10 imágenes por etapa. JPG o PNG, de máximo 2 MB y 4000 × 4000 px cada una. Las imágenes que quites se eliminarán al guardar.</small>
                </section>
            </div>
            <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn-modern btn-success-modern"><i class="fas fa-save"></i> Registrar reparación</button></div>
        </form>
    </div>
</div>

<div class="modal fade apartado-modal" id="entregaExternaModal" tabindex="-1" role="dialog" aria-labelledby="entregaExternaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"><form class="modal-content" id="formEntregaExterna">
        @csrf
        <div class="modal-header"><div><span class="apartado-modal-badge">Reparación externa</span><h5 class="modal-title" id="entregaExternaTitulo">Marcar como entregado</h5><p class="apartado-modal-subtitle mb-0">Se registrará un egreso por el costo y un ingreso por el precio cobrado.</p></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body">
            <div class="alert alert-danger d-none" id="entregaExternaErrores" role="alert" aria-live="polite"></div>
            <div class="row">
                <div class="col-md-6 form-group-modern"><label for="entregaExternaCompra">Precio de compra / Costo de reparación</label><input id="entregaExternaCompra" name="precio_compra" class="form-control" type="number" min="0" max="9999999999.99" step="0.01" required><small class="form-text text-muted">Lo que nos cobraron. Se registra como egreso.</small></div>
                <div class="col-md-6 form-group-modern"><label for="entregaExternaVenta">Precio de venta / Cobro al cliente</label><input id="entregaExternaVenta" name="precio_venta" class="form-control" type="number" min="0" max="9999999999.99" step="0.01" required><small class="form-text text-muted">Lo que cobraste. Se registra como ingreso.</small></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn-modern btn-success-modern"><i class="fas fa-check"></i> Confirmar entrega</button></div>
    </form></div>
</div>
