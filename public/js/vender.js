$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $('#id_producto').select2();
    $('#id_cliente').select2();

    $('#btnNuevoClienteVenta').on('click', function () {
        $('#nuevo_cliente_nombre').val('');
        $('#nuevo_cliente_telefono').val('');
        $('#nuevo_cliente_observaciones').val('');
        $('#modalNuevoClienteVenta').modal('show');
    });

    $(document).off('click', 'button[name="accion"][value="terminar"]').on('click', 'button[name="accion"][value="terminar"]', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $form = $button.closest('form');

        swal.fire({
            title: 'Tipo de pago',
            text: 'Selecciona cómo se realizó esta venta.',
            icon: 'question',
            input: 'select',
            inputOptions: {
                EFECTIVO: 'Efectivo',
                MERCADO_PAGO: 'Mercado Pago'
            },
            inputPlaceholder: 'Selecciona una opción',
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar',
            inputValidator: function (value) {
                if (!value) {
                    return 'Debes seleccionar un tipo de pago';
                }
            }
        }).then(function (result) {
            if (!result.isConfirmed || !result.value) {
                return;
            }

            $('#tipo_pago_venta').val(result.value);

            $form.find('input[name="accion"][type="hidden"]').remove();
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'accion')
                .val('terminar')
                .appendTo($form);

            $form.get(0).submit();
        });
    });

    $('#formNuevoClienteVenta').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: '/vender/cliente-rapido',
            type: 'post',
            dataType: 'json',
            data: {
                nombre: $('#nuevo_cliente_nombre').val(),
                telefono: $('#nuevo_cliente_telefono').val(),
                observaciones: $('#nuevo_cliente_observaciones').val(),
            },
            success: function (data) {
                if (!data.lSuccess || !data.cliente) {
                    swal.fire({
                        title: 'Error',
                        text: data.cMensaje || 'No se pudo guardar el cliente.',
                        icon: 'error',
                        showConfirmButton: true,
                        confirmButtonText: 'Aceptar',
                    });
                    return;
                }

                var option = new Option(data.cliente.nombre, data.cliente.id, true, true);
                $('#id_cliente').append(option).val(data.cliente.id).trigger('change');
                $('#modalNuevoClienteVenta').modal('hide');

                swal.fire({
                    title: 'Clientes',
                    text: data.cMensaje,
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonText: 'Aceptar',
                });
            },
            error: function () {
                swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al guardar el cliente.',
                    icon: 'error',
                    showConfirmButton: true,
                    confirmButtonText: 'Aceptar',
                });
            },
        });
    });

    $(document).off('click', '.btnEditarPrecioVenta').on('click', '.btnEditarPrecioVenta', function () {
        var indice = $(this).data('indice');
        var precioActual = $(this).data('precio');
        var nombreProducto = $(this).data('producto') || 'producto';

        swal.fire({
            title: 'Editar precio',
            html: '<div style="font-size:0.95rem;color:#6c757d;">' + nombreProducto + '</div>',
            input: 'number',
            inputValue: precioActual,
            inputAttributes: {
                min: 0.01,
                step: 0.01
            },
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            inputValidator: function (value) {
                if (!value || parseFloat(value) <= 0) {
                    return 'Ingresa un precio válido mayor a 0';
                }
            }
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: '/precioProductoVenta',
                type: 'post',
                dataType: 'json',
                data: {
                    indice: indice,
                    precio_venta: result.value
                },
                success: function () {
                    window.location.reload();
                },
                error: function (xhr) {
                    var msg = 'No se pudo actualizar el precio.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    swal.fire({
                        title: 'Error',
                        text: msg,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    });

});


$( "#id_producto" ).on( "change", function() {
    var id = $("#id_producto").val()
    if(id > 0){
        $("#codigo").val(id)
        $("#formCodigo").submit()
    }
  } );

// Imprimir ticket: intentar generar y descargar PDF usando html2canvas + jsPDF, fallback a ventana de impresión
function printTicket() {
    var items = window.ventaData || [];
    var total = (window.ventaTotal !== undefined) ? window.ventaTotal : 0;
    var fecha = window.ventaFecha || '';
    var usuario = window.ventaUsuario || '';

    var html = '<div style="font-family:Arial,Helvetica,sans-serif;padding:12px;color:#000;width:320px">';
    html += '<h2 style="text-align:center;margin:0 0 6px">TICKET</h2>';
    if (usuario) html += '<div style="font-size:12px;margin-bottom:6px;text-align:center">Vendedor: ' + usuario + '</div>';
    if (fecha) html += '<div style="font-size:12px;margin-bottom:6px;text-align:center">Fecha: ' + fecha + '</div>';
    html += '<table style="width:100%;border-collapse:collapse;margin-top:6px">';
    html += '<thead><tr><th style="width:20%;text-align:left">Cod</th><th style="width:50%;text-align:left">Producto</th><th style="width:10%;text-align:right">Cant</th><th style="width:20%;text-align:right">P.Unit</th></tr></thead><tbody>';

    items.forEach(function(p){
        var codigo = p.codigo_barras || '';
        var desc = p.descripcion || '';
        var qty = p.cantidad || p.cantidad_venta || 1;
        var precio = parseFloat(p.precio_venta || p.precio || 0) || 0;
        html += '<tr>';
        html += '<td style="padding:4px 2px;">' + codigo + '</td>';
        html += '<td style="padding:4px 2px;">' + desc + '</td>';
        html += '<td style="padding:4px 2px;text-align:right">' + qty + '</td>';
        html += '<td style="padding:4px 2px;text-align:right">$' + precio.toFixed(2) + '</td>';
        html += '</tr>';
    });

    html += '</tbody></table>';
    html += '<div style="text-align:right;font-weight:700;margin-top:8px">Total: $' + parseFloat(total).toFixed(2) + '</div>';
    html += '<p style="text-align:center;margin-top:12px;font-size:12px">Gracias por su compra</p>';
    html += '</div>';

    if (window.html2canvas && window.jspdf) {
        var temp = document.createElement('div');
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        temp.innerHTML = html;
        document.body.appendChild(temp);

        html2canvas(temp, { scale: 2, useCORS: true }).then(function(canvas) {
            const { jsPDF } = window.jspdf;
            var pdf = new jsPDF({ unit: 'pt', format: 'a4', orientation: 'p' });
            var pageWidth = pdf.internal.pageSize.getWidth();
            var pageHeight = pdf.internal.pageSize.getHeight();
            var margin = 20;
            var imgWidth = pageWidth - margin * 2;
            var scale = imgWidth / canvas.width;
            var imgHeight = canvas.height * scale;

            if (imgHeight <= pageHeight - margin * 2) {
                pdf.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
            } else {
                var remainingHeight = canvas.height;
                var position = 0;
                var sliceCanvas = document.createElement('canvas');
                var sliceCtx = sliceCanvas.getContext('2d');

                while (remainingHeight > 0) {
                    var sliceHeight = Math.min(remainingHeight, Math.round((pageHeight - margin * 2) / scale));
                    sliceCanvas.width = canvas.width;
                    sliceCanvas.height = sliceHeight;
                    sliceCtx.clearRect(0, 0, sliceCanvas.width, sliceCanvas.height);
                    sliceCtx.drawImage(canvas, 0, position, canvas.width, sliceHeight, 0, 0, canvas.width, sliceHeight);
                    var pageData = sliceCanvas.toDataURL('image/png');
                    if (pdf.getNumberOfPages() > 0) pdf.addPage();
                    pdf.addImage(pageData, 'PNG', margin, margin, imgWidth, sliceHeight * scale);
                    remainingHeight -= sliceHeight;
                    position += sliceHeight;
                }
            }

            // show PDF in new tab
            var blob = pdf.output('blob');
            var url = URL.createObjectURL(blob);
            window.open(url, '_blank');
            // revoke after a minute
            setTimeout(function(){ URL.revokeObjectURL(url); }, 60000);
            document.body.removeChild(temp);
        }).catch(function(err){
            if (temp.parentNode) document.body.removeChild(temp);
            alert('Error al generar PDF, se abrirá la vista de impresión.');
            var w = window.open('', '_blank', 'width=400,height=600');
            w.document.open(); w.document.write(html); w.document.close();
            setTimeout(function(){ w.focus(); w.print(); }, 300);
        });

        return;
    }

    var w = window.open('', '_blank', 'width=400,height=600');
    if (!w) { alert('Permite ventanas emergentes para imprimir el ticket.'); return; }
    w.document.open();
    w.document.write('<!doctype html><html><head><meta charset="utf-8"><title>Ticket</title></head><body>' + html + '</body></html>');
    w.document.close();
    setTimeout(function(){ w.focus(); w.print(); w.close(); }, 500);
}

// vincular botón
$(document).off('click', '#btnPrintTicket').on('click', '#btnPrintTicket', function(){
    printTicket();
});

// ensure botón vinculado reliably y mostrar errores en consola
$(document).ready(function () {
    $('#btnPrintTicket').off('click').on('click', function (e) {
        e.preventDefault();
        console.log('btnPrintTicket clicked');
        if (typeof printTicket === 'function') {
            try {
                printTicket();
            } catch (err) {
                console.error('Error en printTicket:', err);
                alert('Error al generar el ticket. Revisa la consola.');
            }
        } else {
            console.error('printTicket no está definida');
            alert('Función de impresión no disponible. Revisa la consola.');
        }
    });
});
