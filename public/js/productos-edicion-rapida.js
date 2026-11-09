(function () {
    var guardando = false;
    var imagenTemporal = null;
    var imagenOriginal = '';

    function mostrarImagen(src) {
        var imagen = document.getElementById('edicionRapidaImagen');
        imagen.hidden = !src;
        if (src) imagen.src = src;
        else imagen.removeAttribute('src');
        document.getElementById('edicionRapidaSinImagen').hidden = !!src;
    }

    function liberarImagen() {
        if (imagenTemporal) URL.revokeObjectURL(imagenTemporal);
        imagenTemporal = null;
    }

    window.abrirEdicionRapida = function (id) {
        if (guardando) return;
        var grid = $('#gridProductos');
        var datos = document.getElementById('productosDashboardEdicionRapida');
        var productos = grid.length ? grid.bootstrapTable('getData') : (datos ? JSON.parse(datos.textContent) : []);
        var producto = productos.find(function (row) {
            return Number(row.id) === Number(id);
        });
        if (!producto) return;

        var modal = $('#edicionRapidaModal');
        var form = document.getElementById('formEdicionRapida');
        form.reset();
        liberarImagen();
        form.action = modal.attr('data-url') + '/' + Number(id) + '/edicion-rapida';
        $('#edicionRapidaErrores').addClass('d-none').text('');
        $('#edicionRapidaDescripcion').text(producto.descripcion);
        $('#rapidaCodigo').val(producto.codigo_barras);
        ['precio_compra', 'precio_venta', 'existencia'].forEach(function (campo) {
            $('#rapida_' + campo).val(producto[campo]);
        });
        imagenOriginal = producto.img ? modal.attr('data-imagenes') + '/' + encodeURIComponent(producto.img) : '';
        mostrarImagen(imagenOriginal);
        modal.modal('show');
    };

    $(function () {
        $('#edicionRapidaModal').on('shown.bs.modal', function () {
            $('#rapidaCodigo').trigger('focus');
        }).on('hide.bs.modal', function (event) {
            if (guardando) event.preventDefault();
        }).on('hidden.bs.modal', liberarImagen);

        $('#rapidaImg').on('change', function () {
            liberarImagen();
            var archivo = this.files[0];
            var errores = $('#edicionRapidaErrores').addClass('d-none').text('');
            if (!archivo) {
                mostrarImagen(imagenOriginal);
                return;
            }
            if (archivo.size > 2 * 1024 * 1024 || !/^image\/(jpeg|png|gif|webp)$/.test(archivo.type)) {
                errores.removeClass('d-none').text('Selecciona una imagen JPG, PNG, GIF o WebP de hasta 2 MB.');
                this.value = '';
                mostrarImagen(imagenOriginal);
                return;
            }
            imagenTemporal = URL.createObjectURL(archivo);
            mostrarImagen(imagenTemporal);
        });

        $('#formEdicionRapida').on('submit', function (event) {
            event.preventDefault();
            if (guardando || !this.reportValidity()) return;
            var datos = new FormData(this);
            var botones = $('#edicionRapidaModal button');
            var guardar = $('#guardarEdicionRapida');
            var textoGuardar = guardar.html();
            $('#edicionRapidaErrores').addClass('d-none').text('');
            guardando = true;
            botones.prop('disabled', true);
            guardar.text('Guardando…');

            $.ajax({
                url: this.action,
                type: 'POST',
                data: datos,
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: { Accept: 'application/json' }
            }).done(function () {
                guardando = false;
                $('#edicionRapidaModal').modal('hide');
                if ($('#gridProductos').length) {
                    $('#gridProductos').bootstrapTable('refresh', { silent: true });
                    Swal.fire({ icon: 'success', title: 'Producto actualizado', timer: 1600, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'success', title: 'Producto actualizado', timer: 1600, showConfirmButton: false })
                        .then(function () { window.location.reload(); });
                }
            }).fail(function (xhr) {
                var respuesta = xhr.responseJSON || {};
                var mensajes = [];
                if (respuesta.errors) {
                    Object.keys(respuesta.errors).forEach(function (campo) {
                        mensajes = mensajes.concat(respuesta.errors[campo]);
                    });
                }
                if (!mensajes.length) {
                    mensajes.push(respuesta.cMensaje || (xhr.status === 419
                        ? 'La sesión expiró. Recarga la página antes de guardar.'
                        : 'No se pudo guardar el producto. Revisa tu conexión e inténtalo de nuevo.'));
                }
                $('#edicionRapidaErrores').removeClass('d-none').text(mensajes.join(' '));
            }).always(function () {
                guardando = false;
                botones.prop('disabled', false);
                guardar.html(textoGuardar);
            });
        });
    });
}());
