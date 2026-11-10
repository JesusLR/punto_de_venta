(function () {
    'use strict';
    $(function () {
        var app = $('#reparacionesApp');
        if (!app.length) return;
        var piezas = [];
        var resultados = [];
        var guardando = false;
        var entregando = false;
        var renombrando = false;
        var busqueda = null;
        var peticion = null;
        var token = $('meta[name="csrf-token"]').attr('content');
        var peticionGrid = null, turnoGrid = 0;
        function cargarGrid(pagina) {
            var turno = ++turnoGrid;
            if (peticionGrid) peticionGrid.abort();
            var filtros = $('#filtrosReparaciones');
            var datos = filtros.serializeArray();
            datos.push({ name: 'page', value: pagina || 1 });
            $('#reparacionesGridError').addClass('d-none').text('');
            $('#reparacionesResultados').attr('aria-busy', 'true').addClass('reparaciones-cargando');
            peticionGrid = $.getJSON(filtros.attr('action'), $.param(datos))
                .done(function (respuesta) {
                    if (turno !== turnoGrid) return;
                    $('#reparacionesResultados').html(respuesta.html);
                    var url = new URL(filtros.attr('action'), window.location.href);
                    url.search = $.param(datos);
                    if (url.origin === window.location.origin) window.history.replaceState(null, '', url.pathname + url.search);
                }).fail(function (xhr, estado) {
                    if (estado === 'abort' || turno !== turnoGrid) return;
                    if (xhr.status === 401 || xhr.status === 403) $('#reparacionesResultados').empty();
                    $('#reparacionesGridError').removeClass('d-none').text(mensajeError(xhr));
                }).always(function () {
                    if (turno === turnoGrid) $('#reparacionesResultados').attr('aria-busy', 'false').removeClass('reparaciones-cargando');
                });
        }
        $('#filtrosReparaciones').on('submit', function (event) { event.preventDefault(); cargarGrid(1); });
        $('#filtrosReparaciones select').on('change', function () { cargarGrid(1); });
        $(document).on('click', '#reparacionesResultados .pagination a', function (event) {
            event.preventDefault();
            cargarGrid(Number(new URL(this.href, window.location.href).searchParams.get('page')) || 1);
        });

        function mensajeError(xhr) {
            var respuesta = xhr.responseJSON || {};
            var mensajes = [];
            if (respuesta.errors) Object.keys(respuesta.errors).forEach(function (campo) { mensajes = mensajes.concat(respuesta.errors[campo]); });
            return mensajes.join(' ') || respuesta.cMensaje || (xhr.status === 419 ? 'La sesión expiró. Recarga la página.' : 'No se pudo completar la operación. Revisa tu conexión y tus permisos.');
        }

        function dibujarPiezas() {
            var cuerpo = $('#piezasReparacionSeleccionadas').empty();
            $('#reparacionSinPiezas').toggle(!piezas.length);
            piezas.forEach(function (pieza) {
                var fila = $('<tr>');
                $('<td>').text(pieza.codigo_barras + ' · ' + pieza.descripcion).appendTo(fila);
                $('<td>').text(pieza.existencia).appendTo(fila);
                $('<td>').append($('<input>', { type: 'number', min: 1, max: Math.floor(Number(pieza.existencia)), step: 1, required: true, 'class': 'form-control', 'aria-label': 'Unidades a reparar: ' + pieza.descripcion })
                    .val(pieza.cantidad).on('input', function () { pieza.cantidad = this.value; })).appendTo(fila);
                $('<td>').append($('<button>', { type: 'button', 'class': 'btn btn-outline-danger btn-sm', 'aria-label': 'Quitar ' + pieza.descripcion }).text('Quitar').on('click', function () {
                    if (guardando) return;
                    piezas = piezas.filter(function (item) { return item.id !== pieza.id; });
                    dibujarPiezas(); dibujarResultados();
                })).appendTo(fila);
                cuerpo.append(fila);
            });
        }

        function dibujarResultados() {
            var lista = $('#resultadosPiezasReparacion').empty();
            if (!resultados.length) return lista.text('No se encontraron piezas con unidades disponibles.');
            resultados.forEach(function (producto) {
                var agregado = piezas.some(function (pieza) { return pieza.id === producto.id; });
                $('<button>', { type: 'button', 'class': 'list-group-item list-group-item-action', disabled: agregado || guardando })
                    .text(producto.codigo_barras + ' · ' + producto.descripcion + ' — Disponible: ' + producto.existencia + (agregado ? ' · Agregado' : ' · Agregar'))
                    .on('click', function () {
                        if (guardando || piezas.some(function (pieza) { return pieza.id === producto.id; })) return;
                        piezas.push(Object.assign({}, producto, { cantidad: 1 }));
                        dibujarPiezas(); dibujarResultados();
                    }).appendTo(lista);
            });
        }

        function buscar() {
            if (peticion) peticion.abort();
            peticion = $.getJSON(app.attr('data-productos'), { q: $('#buscarPiezaReparacion').val() })
                .done(function (data) { resultados = data; dibujarResultados(); })
                .fail(function (xhr, estado) { if (estado !== 'abort') $('#resultadosPiezasReparacion').text(mensajeError(xhr)); });
        }

        $('#buscarPiezaReparacion').on('input', function () { clearTimeout(busqueda); busqueda = setTimeout(buscar, 250); })
            .on('keydown', function (event) { if (event.key === 'Enter') { event.preventDefault(); clearTimeout(busqueda); buscar(); } });
        $('#nuevaReparacionModal').on('shown.bs.modal', function () {
            dibujarPiezas(); buscar(); $('#buscarPiezaReparacion').trigger('focus');
        }).on('hide.bs.modal', function (event) { if (guardando) event.preventDefault(); });

        $('#formReparacion').on('submit', function (event) {
            event.preventDefault();
            if (guardando || !this.reportValidity()) return;
            if (!piezas.length) return $('#reparacionErrores').removeClass('d-none').text('Agrega al menos un producto.');
            var data = {
                solicitud_id: this.elements.solicitud_id.value,
                nombre: this.elements.nombre.value,
                total: this.elements.total.value, observaciones: this.elements.observaciones.value,
                productos: piezas.map(function (pieza) { return { producto_id: pieza.id, cantidad: pieza.cantidad }; })
            };
            guardando = true;
            $('#reparacionErrores').addClass('d-none').text('');
            $('#formReparacion button, #formReparacion input, #formReparacion textarea').prop('disabled', true);
            $.ajax({ url: app.attr('data-guardar'), type: 'POST', contentType: 'application/json', dataType: 'json', data: JSON.stringify(data), headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) {
                    guardando = false;
                    $('#formReparacion button, #formReparacion input, #formReparacion textarea').prop('disabled', false);
                    dibujarResultados();
                    $('#reparacionErrores').removeClass('d-none').text(mensajeError(xhr));
                });
        });

        $(document).on('click', '.btn-nombre-reparacion', function () {
            if (renombrando) return;
            document.getElementById('formNombreReparacion').action = $(this).attr('data-url');
            $('#nombreReparacionInput').val($(this).attr('data-nombre') || '');
            $('#nombreReparacionErrores').addClass('d-none').text('');
            $('#nombreReparacionModal').modal('show');
        });
        $('#nombreReparacionModal').on('shown.bs.modal', function () { $('#nombreReparacionInput').trigger('focus'); })
            .on('hide.bs.modal', function (event) { if (renombrando) event.preventDefault(); });
        $('#formNombreReparacion').on('submit', function (event) {
            event.preventDefault();
            if (renombrando || !this.reportValidity()) return;
            var data = { nombre: this.elements.nombre.value };
            renombrando = true;
            $('#formNombreReparacion button').prop('disabled', true);
            $('#nombreReparacionErrores').addClass('d-none').text('');
            $.ajax({ url: this.action, type: 'POST', data: data, dataType: 'json', headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) {
                    renombrando = false;
                    $('#formNombreReparacion button').prop('disabled', false);
                    $('#nombreReparacionErrores').removeClass('d-none').text(mensajeError(xhr));
                });
        });

        $(document).on('click', '.btn-cancelar-reparacion', async function () {
            if (entregando) return;
            var boton = $(this);
            entregando = true;
            var respuesta = await Swal.fire({ icon: 'warning', title: '¿Cancelar la reparación #' + boton.attr('data-folio') + '?',
                text: boton.attr('data-tipo') === 'EXTERNA' ? 'La reparación quedará cancelada, sin generar ingresos ni egresos.' : 'Confirma que las piezas están de vuelta. Todas sus unidades quedarán disponibles para venta sin generar un egreso.',
                showCancelButton: true, confirmButtonText: 'Sí, cancelar reparación', cancelButtonText: 'Volver' });
            if (!respuesta.isConfirmed) { entregando = false; return; }
            boton.prop('disabled', true);
            $.ajax({ url: boton.attr('data-url'), type: 'POST', dataType: 'json', headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) { entregando = false; boton.prop('disabled', false); Swal.fire({ icon: 'error', title: 'No se pudo cancelar', text: mensajeError(xhr) }); });
        });

        $(document).on('click', '.btn-entregar-reparacion', async function () {
            if (entregando) return;
            var boton = $(this);
            entregando = true;
            var respuesta = await Swal.fire({ icon: 'question', title: '¿Recibiste las piezas de la reparación #' + boton.attr('data-folio') + '?',
                text: 'Se devolverán todas sus unidades al inventario y se registrará un egreso de $' + boton.attr('data-total') + '. Esta operación se realiza una sola vez.',
                showCancelButton: true, confirmButtonText: 'Sí, marcar entregado', cancelButtonText: 'Cancelar' });
            if (!respuesta.isConfirmed) { entregando = false; return; }
            boton.prop('disabled', true);
            $.ajax({ url: boton.attr('data-url'), type: 'POST', dataType: 'json', headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } })
                .done(function () { window.location.reload(); })
                .fail(function (xhr) { entregando = false; boton.prop('disabled', false); Swal.fire({ icon: 'error', title: 'No se pudo recibir', text: mensajeError(xhr) }); });
        });
    });
}());
