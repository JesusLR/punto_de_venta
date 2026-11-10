(function () {
    'use strict';
    $(function () {
        var anterior = null;
        var pdfUrl = null;
        function actualizarPdf() {
            if (pdfUrl) $('#reparacionPdfAbrir').attr('href', pdfUrl + '?imagenes=' + ($('#reparacionPdfIncluirImagenes').prop('checked') ? '1' : '0'));
        }
        $(document).on('click', '.btn-ver-imagen-reparacion', function () {
            var modalOrigen = $(this).closest('.modal');
            anterior = modalOrigen.length ? modalOrigen.attr('id') : null;
            $('#reparacionImagenError').prop('hidden', true);
            $('#reparacionImagenGrande').prop('hidden', false).attr('src', $(this).attr('data-url'));
            if (anterior) modalOrigen.one('hidden.bs.modal', function () { $('#reparacionImagenModal').modal('show'); }).modal('hide');
            else $('#reparacionImagenModal').modal('show');
        });
        $('#reparacionImagenGrande').on('error', function () { $(this).prop('hidden', true); $('#reparacionImagenError').prop('hidden', false); });
        $('#reparacionImagenModal').on('hidden.bs.modal', function () {
            $('#reparacionImagenGrande').removeAttr('src');
            if (anterior) { var id = anterior; anterior = null; $('#' + id).modal('show'); }
        });
        $(document).on('click', '.btn-ver-pdf-reparacion', function () {
            pdfUrl = $(this).attr('data-url');
            actualizarPdf();
            $('#reparacionPdfOpcionesModal').modal('show');
        });
        $('#reparacionPdfIncluirImagenes').on('change', actualizarPdf);
        $('#reparacionPdfAbrir').on('click', function () { $('#reparacionPdfOpcionesModal').modal('hide'); });
    });
}());
