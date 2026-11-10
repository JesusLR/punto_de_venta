<div class="modal fade apartado-modal" id="reparacionImagenModal" tabindex="-1" role="dialog" aria-labelledby="reparacionImagenTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document"><div class="modal-content">
        <div class="modal-header"><div><span class="apartado-modal-badge"><i class="fas fa-image"></i> Vista previa</span><h5 class="modal-title" id="reparacionImagenTitulo">Imagen de la reparación</h5></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body text-center"><img id="reparacionImagenGrande" alt="Imagen ampliada de la reparación"><p id="reparacionImagenError" class="text-muted" hidden>No se pudo cargar la imagen.</p></div>
        <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cerrar</button></div>
    </div></div>
</div>
<div class="modal fade apartado-modal" id="reparacionPdfOpcionesModal" tabindex="-1" role="dialog" aria-labelledby="reparacionPdfTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
        <div class="modal-header"><div><span class="apartado-modal-badge"><i class="fas fa-file-pdf"></i> Documento</span><h5 class="modal-title" id="reparacionPdfTitulo">Opciones del PDF</h5></div><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="reparacionPdfIncluirImagenes" checked><label class="custom-control-label" for="reparacionPdfIncluirImagenes">Incluir imágenes de antes y después</label></div><p class="form-text text-muted mt-3">El comprobante se abrirá en una pestaña del navegador, como los PDF de Apartados.</p></div>
        <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button><a id="reparacionPdfAbrir" target="_blank" rel="noopener" class="btn-modern btn-success-modern"><i class="fas fa-file-pdf"></i> Ver PDF</a></div>
    </div></div>
</div>
