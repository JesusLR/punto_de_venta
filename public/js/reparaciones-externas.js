(function () {
    'use strict';
    $(function () {
        var form = document.getElementById('formReparacionExterna');
        if (!form) return;
        var guardando = false, entregando = false, peticion = null, temporizador = null;
        var previews = [];
        var clientesEncontrados = [];
        var editando = false, existentes = [], quitar = [], urlGuardar = $(form).attr('data-url');
        var token = $('meta[name="csrf-token"]').attr('content');

        function error(xhr) {
            var data = xhr.responseJSON || {}, mensajes = [];
            if (data.errors) Object.keys(data.errors).forEach(function (campo) { mensajes = mensajes.concat(data.errors[campo]); });
            return mensajes.join(' ') || (xhr.status === 413 ? 'Las imágenes superan el límite de carga del servidor. Selecciona menos imágenes o archivos más pequeños.' : xhr.status === 419 ? 'La sesión expiró. Recarga la página.' : 'No se pudo guardar. Revisa tu conexión y tus permisos.');
        }
        function contacto() {
            $('#externaContactoCaptura').prop('hidden', editando).find('input, select').prop('disabled', editando);
            $('#externaContactoResumen').prop('hidden', !editando);
            if (editando) return;
            var manual = $('#externaOrigen').val() === 'manual';
            $('#externaContactoCatalogo').prop('hidden', manual);
            $('#externaCliente').prop('disabled', manual).prop('required', !manual);
            $('#externaContactoManual').prop('hidden', !manual);
            $('#externaNombre').prop('disabled', !manual).prop('required', manual);
            $('#externaTelefono').prop('required', manual);
            $('#externaTelefonoOpcional').prop('hidden', manual);
        }
        function clientes() {
            if (editando) return;
            if (peticion) peticion.abort();
            var seleccionado = $('#externaCliente').val();
            var nombreSeleccionado = $('#externaCliente option:selected').text();
            peticion = $.getJSON($(form).attr('data-clientes'), { q: $('#externaBuscarCliente').val() })
                .done(function (data) {
                    clientesEncontrados = data;
                    var select = $('#externaCliente').empty().append(new Option('Selecciona un cliente', ''));
                    data.forEach(function (cliente) { select.append(new Option(cliente.nombre + (cliente.telefono ? ' · ' + cliente.telefono : ''), cliente.id)); });
                    if (seleccionado && !data.some(function (cliente) { return String(cliente.id) === seleccionado; })) select.append(new Option(nombreSeleccionado, seleccionado));
                    select.val(seleccionado || '');
                }).fail(function (xhr, estado) { if (estado !== 'abort') $('#reparacionExternaErrores').removeClass('d-none').text(error(xhr)); });
        }
        function liberarPreviews() {
            previews.forEach(function (src) { URL.revokeObjectURL(src); });
            previews = []; $('#externaVistaImagenesANTES, #externaVistaImagenesDESPUES').empty();
        }
        function dibujarFotos() {
            liberarPreviews();
            ['ANTES', 'DESPUES'].forEach(function (etapa) {
                var input = document.getElementById('externaImagenes' + etapa);
                var archivos = Array.from(input.files);
                var conservadas = existentes.filter(function (imagen) { return imagen.etapa === etapa && quitar.indexOf(imagen.id) === -1; }).length;
                if (archivos.length + conservadas > 10 || archivos.some(function (archivo) { return archivo.size > 2 * 1024 * 1024 || !/^image\/(jpeg|png)$/.test(archivo.type); })) {
                    input.value = '';
                    $('#reparacionExternaErrores').removeClass('d-none').text('Cada etapa admite hasta 10 imágenes JPG o PNG de máximo 2 MB cada una, contando las conservadas.');
                    return;
                }
                archivos.forEach(function (archivo, index) {
                    var src = URL.createObjectURL(archivo); previews.push(src);
                    $('<img>', { src: src, alt: 'Nueva imagen ' + (index + 1) }).appendTo('#externaVistaImagenes' + etapa);
                });
            });
        }
        function dibujarExistentes() {
            $('#externaExistentesANTES, #externaExistentesDESPUES').empty();
            existentes.forEach(function (imagen) {
                var marcada = quitar.indexOf(imagen.id) !== -1;
                var card = $('<div>', { 'class': 'reparacion-imagen-card' + (marcada ? ' imagen-por-quitar' : '') });
                $('<button>', { type: 'button', 'class': 'btn-ver-imagen-reparacion', 'data-url': imagen.url, 'aria-label': 'Ampliar imagen' })
                    .append($('<img>', { src: imagen.url, alt: 'Imagen ' + (imagen.etapa === 'ANTES' ? 'antes' : 'después') })).appendTo(card);
                $('<button>', { type: 'button', 'class': 'btn btn-outline-danger btn-sm' }).text(marcada ? 'Conservar' : 'Quitar').on('click', function () {
                    if (guardando) return;
                    quitar = marcada ? quitar.filter(function (id) { return id !== imagen.id; }) : quitar.concat(imagen.id);
                    dibujarExistentes(); dibujarFotos();
                }).appendTo(card);
                $('#externaExistentes' + imagen.etapa).append(card);
            });
        }
        $('#btnNuevaReparacionExterna').on('click', function () {
            if (guardando) return;
            form.reset(); editando = false; existentes = []; quitar = []; urlGuardar = $(form).attr('data-url');
            $('#reparacionExternaTitulo').text('Nueva reparación externa');
            $('#reparacionExternaErrores').addClass('d-none').text('');
            dibujarExistentes(); contacto(); $('#nuevaReparacionExternaModal').modal('show');
        });
        $(document).on('click', '.btn-editar-reparacion-externa', function () {
            if (guardando) return;
            var boton = $(this).prop('disabled', true);
            $.getJSON(boton.attr('data-url')).done(function (data) {
                form.reset(); editando = true; existentes = data.imagenes; quitar = []; urlGuardar = data.url;
                $('#reparacionExternaTitulo').text('Editar reparación externa');
                $('#externaNombreReparacion').val(data.nombre || ''); $('#externaObservaciones').val(data.observaciones);
                $('#externaContactoResumen').empty().append($('<strong>').text(data.contacto_nombre)).append($('<p>', { 'class': 'text-muted' }).text(data.contacto_telefono || ''));
                $('#reparacionExternaErrores').addClass('d-none').text('');
                dibujarExistentes(); contacto(); $('#nuevaReparacionExternaModal').modal('show');
            }).fail(function (xhr) { Swal.fire({ icon: 'error', title: 'No se pudo cargar la reparación', text: error(xhr) }); })
                .always(function () { boton.prop('disabled', false); });
        });
        $('#externaOrigen').on('change', contacto);
        $('#externaCliente').on('change', function () {
            var id = this.value;
            var cliente = clientesEncontrados.find(function (item) { return String(item.id) === id; });
            if (cliente) $('#externaTelefono').val(cliente.telefono || '');
        });
        $('#externaBuscarCliente').on('input', function () { clearTimeout(temporizador); temporizador = setTimeout(clientes, 250); })
            .on('keydown', function (event) { if (event.key === 'Enter') { event.preventDefault(); clearTimeout(temporizador); clientes(); } });
        $('.externa-imagenes').on('change', dibujarFotos);
        $('#nuevaReparacionExternaModal').on('shown.bs.modal', function () { contacto(); clientes(); dibujarFotos(); })
            .on('hide.bs.modal', function (event) { if (guardando) event.preventDefault(); }).on('hidden.bs.modal', liberarPreviews);
        $(form).on('submit', function (event) {
            event.preventDefault();
            if (guardando || !this.reportValidity()) return;
            var datos = new FormData(this);
            ['ANTES', 'DESPUES'].forEach(function (etapa) {
                if (!document.getElementById('externaImagenes' + etapa).files.length) datos.delete('imagenes_' + etapa.toLowerCase() + '[]');
            });
            quitar.forEach(function (id) { datos.append('eliminar_imagenes[]', id); });
            guardando = true;
            $('#reparacionExternaErrores').addClass('d-none').text('');
            $(form).find('input, select, textarea, button').prop('disabled', true);
            $.ajax({ url: urlGuardar, type: 'POST', data: datos, processData: false, contentType: false, dataType: 'json', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) { guardando = false; $(form).find('input, select, textarea, button').prop('disabled', false); contacto(); $('#reparacionExternaErrores').removeClass('d-none').text(error(xhr)); });
        });

        $(document).on('click', '.btn-entregar-externa', function () {
            if (entregando) return;
            var entrega = document.getElementById('formEntregaExterna'); entrega.reset(); entrega.action = $(this).attr('data-url');
            $('#entregaExternaTitulo').text('Entregar reparación #' + $(this).attr('data-folio'));
            $('#entregaExternaErrores').addClass('d-none').text('');
            $('#entregaExternaModal').modal('show');
        });
        $('#entregaExternaModal').on('hide.bs.modal', function (event) { if (entregando) event.preventDefault(); });
        $('#formEntregaExterna').on('submit', function (event) {
            event.preventDefault();
            if (entregando || !this.reportValidity()) return;
            var datos = { precio_compra: this.elements.precio_compra.value, precio_venta: this.elements.precio_venta.value };
            entregando = true;
            $('#entregaExternaErrores').addClass('d-none').text('');
            $('#formEntregaExterna button, #formEntregaExterna input').prop('disabled', true);
            $.ajax({ url: this.action, type: 'POST', data: datos, dataType: 'json', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) { entregando = false; $('#formEntregaExterna button, #formEntregaExterna input').prop('disabled', false); $('#entregaExternaErrores').removeClass('d-none').text(error(xhr)); });
        });
        contacto();
    });
}());
