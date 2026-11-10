(function () {
    'use strict';
    var guardando = false;
    var temporal = null;
    var original = '';
    var editando = false;
    var temporalesGaleria = [];
    var cantidadGaleria = 0;
    var quitarGaleria = [];
    var galeriaGuardada = [];
    var quitarPrincipal = false;
    function dibujarGaleriaGuardada() {
        var modal = $('#productoFormularioModal');
        $('#productoGaleriaActual').empty();
        cantidadGaleria = galeriaGuardada.length - quitarGaleria.length;
        galeriaGuardada.forEach(function (archivo) {
            var quitar = quitarGaleria.indexOf(archivo) !== -1;
            var card = $('<div>', { 'class': 'producto-imagen-card' + (quitar ? ' producto-imagen-quitar' : '') });
            $('<img>', { src: modal.attr('data-imagenes') + '/' + encodeURIComponent(archivo), alt: 'Imagen adicional guardada' }).appendTo(card);
            $('<button>', { type: 'button', 'class': 'btn btn-outline-danger btn-sm' }).text(quitar ? 'Conservar' : 'Quitar').on('click', function () {
                if (guardando) return;
                quitarGaleria = quitar ? quitarGaleria.filter(function (nombre) { return nombre !== archivo; }) : quitarGaleria.concat(archivo);
                dibujarGaleriaGuardada();
            }).appendTo(card);
            $('#productoGaleriaActual').append(card);
        });
    }
    function liberarGaleria() {
        temporalesGaleria.forEach(function (src) { URL.revokeObjectURL(src); });
        temporalesGaleria = []; $('#productoGaleriaPrevia').empty();
    }

    function liberarImagen() {
        if (temporal) URL.revokeObjectURL(temporal);
        temporal = null;
    }

    function mostrarImagen(src) {
        var imagen = document.getElementById('productoModalImagen');
        imagen.hidden = !src;
        if (src) imagen.src = src;
        else imagen.removeAttribute('src');
        document.getElementById('productoModalSinImagen').hidden = !!src;
    }

    function calcularGanancia() {
        var compra = parseFloat($('#productoModal_precio_compra').val()) || 0;
        var venta = parseFloat($('#productoModal_precio_venta').val()) || 0;
        var indicador = $('#productoModalGanancia');
        indicador.toggle(compra > 0 && venta > 0);
        if (compra > 0 && venta > 0) {
            $('#productoModalMargen').text(((venta - compra) / compra * 100).toFixed(2) + '%');
            $('#productoModalUtilidad').text((venta - compra).toFixed(2));
            indicador.toggleClass('profit-positive', venta > compra).toggleClass('profit-negative', venta <= compra);
        }
    }

    window.abrirProductoModal = function (id) {
        if (guardando) return;
        var modal = $('#productoFormularioModal');
        var form = document.getElementById('formProductoModal');
        var producto = id ? $('#gridProductos').bootstrapTable('getData').find(function (row) {
            return Number(row.id) === Number(id);
        }) : null;
        if (id && !producto) return;
        editando = !!producto;
        form.reset();
        liberarImagen();
        liberarGaleria();
        galeriaGuardada = producto && Array.isArray(producto.imagenes_adicionales) ? producto.imagenes_adicionales : [];
        quitarGaleria = []; quitarPrincipal = false;
        dibujarGaleriaGuardada();
        modal.find('option[data-historico]').remove();
        $('#productoFormularioErrores').addClass('d-none').text('');
        form.action = editando ? modal.attr('data-base-url') + '/' + Number(id) + '/modal' : modal.attr('data-create-url');
        $('#productoFormularioTitulo').text(editando ? 'Editar producto' : 'Nuevo producto');
        ['codigo_barras', 'descripcion', 'precio_compra', 'precio_venta', 'existencia', 'id_proveedor', 'id_categoria', 'id_material'].forEach(function (campo) {
            var input = form.elements.namedItem(campo);
            var value = producto ? producto[campo] : '';
            if (producto && input.tagName === 'SELECT') {
                value = value == null ? '0' : String(value);
                // Preserve classifications that are no longer in the active catalogs.
                if (!Array.from(input.options).some(function (option) { return option.value === value; })) {
                    var option = new Option(producto[campo.replace('id_', '')] || 'Clasificación actual', value);
                    option.setAttribute('data-historico', '1');
                    input.add(option);
                }
            }
            input.value = value == null ? '' : value;
        });
        original = producto && producto.img ? modal.attr('data-imagenes') + '/' + encodeURIComponent(producto.img) : '';
        $('#productoQuitarPrincipal').prop('hidden', !original).text('Quitar imagen guardada');
        mostrarImagen(original);
        calcularGanancia();
        modal.modal('show');
    };

    $(function () {
        $('#btnNuevoProductoModal').on('click', function () { window.abrirProductoModal(); });
        $('#productoQuitarPrincipal').on('click', function () {
            if (guardando) return;
            quitarPrincipal = !quitarPrincipal;
            $(this).text(quitarPrincipal ? 'Conservar imagen guardada' : 'Quitar imagen guardada');
            mostrarImagen(temporal || (quitarPrincipal ? '' : original));
        });
        $('#productoModal_precio_compra, #productoModal_precio_venta').on('input', calcularGanancia);
        $('#productoFormularioModal').on('shown.bs.modal', function () {
            $('#productoModalCodigo').trigger('focus');
        }).on('hide.bs.modal', function (event) {
            if (guardando) event.preventDefault();
        }).on('hidden.bs.modal', function () { liberarImagen(); liberarGaleria(); });
        $('#productoGaleriaArchivos').on('change', function () {
            liberarGaleria();
            var archivos = Array.from(this.files);
            if (archivos.length + cantidadGaleria > 10 || archivos.some(function (archivo) { return archivo.size > 2 * 1024 * 1024 || !/^image\/(jpeg|png|gif|webp)$/.test(archivo.type); })) {
                this.value = '';
                $('#productoFormularioErrores').removeClass('d-none').text('El producto admite hasta 10 imágenes adicionales JPG, PNG, GIF o WebP de hasta 2 MB cada una.');
                return;
            }
            archivos.forEach(function (archivo) { var src = URL.createObjectURL(archivo); temporalesGaleria.push(src); $('<img>', { src: src, alt: 'Vista previa de imagen adicional' }).appendTo('#productoGaleriaPrevia'); });
        });

        $('#productoModalArchivo').on('change', function () {
            liberarImagen();
            var archivo = this.files[0];
            $('#productoFormularioErrores').addClass('d-none').text('');
            if (!archivo) return mostrarImagen(quitarPrincipal ? '' : original);
            if (archivo.size > 2 * 1024 * 1024 || !/^image\/(jpeg|png|gif|webp)$/.test(archivo.type)) {
                this.value = '';
                $('#productoFormularioErrores').removeClass('d-none').text('Selecciona una imagen JPG, PNG, GIF o WebP de hasta 2 MB.');
                return mostrarImagen(quitarPrincipal ? '' : original);
            }
            temporal = URL.createObjectURL(archivo);
            mostrarImagen(temporal);
        });

        $('#formProductoModal').on('submit', async function (event) {
            event.preventDefault();
            if (guardando || !this.reportValidity()) return;
            if (document.getElementById('productoGaleriaArchivos').files.length + cantidadGaleria > 10) {
                $('#productoFormularioErrores').removeClass('d-none').text('El producto admite hasta 10 imágenes adicionales.');
                return;
            }
            if (Number(this.elements.precio_venta.value) < Number(this.elements.precio_compra.value)) {
                guardando = true;
                var confirmacion = await Swal.fire({
                    icon: 'warning', title: '¿Confirmar?', text: 'El precio de venta es menor al precio de compra',
                    showCancelButton: true, confirmButtonText: 'Continuar', cancelButtonText: 'Cancelar'
                });
                guardando = false;
                if (!confirmacion.isConfirmed) return;
            }
            var datos = new FormData(this);
            if (!document.getElementById('productoGaleriaArchivos').files.length) datos.delete('imagenes_adicionales[]');
            quitarGaleria.forEach(function (archivo) { datos.append('eliminar_imagenes[]', archivo); });
            if (quitarPrincipal) datos.append('quitar_imagen_principal', '1');
            var botones = $('#productoFormularioModal button');
            var guardar = $('#guardarProductoModal');
            var texto = guardar.html();
            $('#productoFormularioErrores').addClass('d-none').text('');
            guardando = true;
            botones.prop('disabled', true);
            guardar.text('Guardando…');
            $.ajax({
                url: this.action, type: 'POST', data: datos, processData: false, contentType: false,
                dataType: 'json', headers: { Accept: 'application/json' }
            }).done(function () {
                guardando = false;
                $('#productoFormularioModal').modal('hide');
                $('#gridProductos').bootstrapTable('refresh', { silent: true });
                Swal.fire({ icon: 'success', title: editando ? 'Producto actualizado' : 'Producto registrado', timer: 1600, showConfirmButton: false });
            }).fail(function (xhr) {
                var respuesta = xhr.responseJSON || {};
                var mensajes = [];
                if (respuesta.errors) Object.keys(respuesta.errors).forEach(function (campo) {
                    mensajes = mensajes.concat(respuesta.errors[campo]);
                });
                if (!mensajes.length) mensajes.push(xhr.status === 419 ? 'La sesión expiró. Recarga la página antes de guardar.' : 'No se pudo guardar el producto. Revisa tu conexión o tus permisos e inténtalo de nuevo.');
                $('#productoFormularioErrores').removeClass('d-none').text(mensajes.join(' '));
            }).always(function () {
                guardando = false;
                botones.prop('disabled', false);
                guardar.html(texto);
            });
        });
    });
}());
