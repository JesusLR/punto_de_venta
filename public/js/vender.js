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
        var telefonoCliente = normalizarTelefonoMx($('#id_cliente option:selected').data('telefono') || '');

        swal.fire({
            title: 'Tipo de pago',
            icon: 'question',
            html: '\
                <div style="text-align:left;">\
                    <label style="font-size:13px;color:#475569;margin-bottom:6px;display:block;">Tipo de pago</label>\
                    <select id="swalTipoPago" class="swal2-select" style="display:block;width:100%;margin:0 0 12px 0;">\
                        <option value="">Selecciona una opción</option>\
                        <option value="EFECTIVO">Efectivo</option>\
                        <option value="MERCADO_PAGO">Mercado Pago</option>\
                    </select>\
                    <label style="display:flex;align-items:center;gap:8px;margin-bottom:10px;cursor:pointer;">\
                        <input id="swalEnviarWhatsapp" type="checkbox" style="width:16px;height:16px;">\
                        <span>Enviar ticket PDF por WhatsApp</span>\
                    </label>\
                    <div id="swalTelefonoWrap" style="display:none;">\
                        <label style="font-size:13px;color:#475569;margin-bottom:6px;display:block;">Teléfono del cliente</label>\
                        <input id="swalTelefonoWhatsapp" class="swal2-input" style="margin:0;width:100%;" maxlength="10" placeholder="10 dígitos" value="' + telefonoCliente + '">\
                        <small style="display:block;margin-top:6px;color:#64748b;">Si marcas esta opción, se enviará el mismo PDF del botón Imprimir ticket.</small>\
                    </div>\
                </div>\
            ',
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            didOpen: function () {
                var check = document.getElementById('swalEnviarWhatsapp');
                var wrap = document.getElementById('swalTelefonoWrap');
                check.addEventListener('change', function () {
                    wrap.style.display = check.checked ? 'block' : 'none';
                });
            },
            preConfirm: function () {
                var tipoPago = document.getElementById('swalTipoPago').value;
                var enviarWhatsapp = document.getElementById('swalEnviarWhatsapp').checked;
                var telefono = normalizarTelefonoMx(document.getElementById('swalTelefonoWhatsapp').value || '');

                if (!tipoPago) {
                    swal.showValidationMessage('Debes seleccionar un tipo de pago');
                    return false;
                }

                if (enviarWhatsapp && telefono.length !== 10) {
                    swal.showValidationMessage('Ingresa un teléfono válido de 10 dígitos para WhatsApp');
                    return false;
                }

                return generarTicketPdfBase64().then(function (ticketPdfBase64) {
                    return {
                        tipoPago: tipoPago,
                        enviarWhatsapp: enviarWhatsapp,
                        telefono: telefono,
                        ticketPdfBase64: enviarWhatsapp ? ticketPdfBase64 : ''
                    };
                }).catch(function () {
                    swal.showValidationMessage('No se pudo generar el PDF del ticket.');
                    return false;
                });
            }
        }).then(function (result) {
            if (!result.isConfirmed || !result.value) {
                return;
            }

            $('#tipo_pago_venta').val(result.value.tipoPago);
            $('#enviar_whatsapp_venta').val(result.value.enviarWhatsapp ? '1' : '0');
            $('#telefono_whatsapp_venta').val(result.value.telefono || '');
            $('#ticket_pdf_base64_venta').val(result.value.ticketPdfBase64 || '');

            $form.find('input[name="accion"][type="hidden"]').remove();
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'accion')
                .val('terminar')
                .appendTo($form);

            swal.fire({
                title: 'Procesando venta...',
                text: 'Por favor espera mientras se termina la venta.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function () {
                    swal.showLoading();
                }
            });

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

// Imprimir ticket con el mismo formato de printTicketVenta
function printTicket() {
    buildTicketPdfBlob().then(function(blob) {
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank');
        if (!win) {
            var a = document.createElement('a');
            a.href = url;
            a.download = 'ticket_' + Date.now() + '.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        } else {
            setTimeout(function(){ URL.revokeObjectURL(url); }, 60000);
        }
    }).catch(function(err) {
        console.error('Error generando PDF:', err);
        alert('No se pudo generar el PDF. Revisa la consola.');
    });
}

function normalizarTelefonoMx(valor) {
    var soloDigitos = (valor || '').toString().replace(/\D/g, '');
    if (soloDigitos.startsWith('521') && soloDigitos.length > 10) return soloDigitos.substring(3);
    if (soloDigitos.startsWith('52') && soloDigitos.length > 10) return soloDigitos.substring(2);
    if (soloDigitos.startsWith('1') && soloDigitos.length === 11) return soloDigitos.substring(1);
    return soloDigitos;
}

function generarTicketPdfBase64() {
    return buildTicketPdfBlob().then(function(blob) {
        return new Promise(function(resolve, reject) {
            var reader = new FileReader();
            reader.onloadend = function() {
                var result = reader.result || '';
                resolve(result.toString().split(',')[1] || '');
            };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    });
}

function buildTicketPdfBlob() {
    var items = window.ventaData || [];
    var total = (window.ventaTotal !== undefined) ? window.ventaTotal : 0;
    var fecha = window.ventaFecha || '';
    var usuario = window.ventaUsuario || '';

    var bodyHtml = '';
    bodyHtml += '<div class="productos-header"><h2>Joyeria Colibri Progreso</h2></div>';
    bodyHtml += '<div class="ticket-meta">';
    if (usuario) bodyHtml += '<div>Vendedor: ' + usuario + '</div>';
    if (fecha) bodyHtml += '<div>Fecha: ' + fecha + '</div>';
    bodyHtml += '</div>';
    bodyHtml += '<table class="table table-bordered" style="width:100%;border-collapse:collapse;margin-top:10px;">';
    bodyHtml += '<thead><tr><th>Cod</th><th>Producto</th><th style="text-align:right">Cant</th><th style="text-align:right">P.Unit</th><th style="text-align:right">Subtotal</th></tr></thead><tbody>';

    items.forEach(function(p){
        var codigo = p.codigo_barras || '';
        var desc = p.descripcion || '';
        var qty = p.cantidad || p.cantidad_venta || 1;
        var precio = parseFloat(p.precio_venta || p.precio || 0) || 0;
        var subtotal = (qty * precio).toFixed(2);
        bodyHtml += '<tr>';
        bodyHtml += '<td>' + codigo + '</td>';
        bodyHtml += '<td>' + desc + '</td>';
        bodyHtml += '<td style="text-align:right">' + qty + '</td>';
        bodyHtml += '<td style="text-align:right">$' + precio.toFixed(2) + '</td>';
        bodyHtml += '<td style="text-align:right">$' + subtotal + '</td>';
        bodyHtml += '</tr>';
    });

    bodyHtml += '</tbody>';
    bodyHtml += '<tfoot><tr><td colspan="3"></td><td style="text-align:right;font-weight:700">Total</td><td style="text-align:right;font-weight:700">$' + parseFloat(total).toFixed(2) + '</td></tr></tfoot>';
    bodyHtml += '</table>';
    bodyHtml += '<p style="text-align:center;margin-top:12px;font-size:12px">Gracias por su compra</p>';

    var temp = document.createElement('div');
    temp.style.position = 'fixed';
    temp.style.left = '-9999px';
    temp.style.top = '0';
    temp.style.width = '800px';
    document.body.appendChild(temp);

    function waitImagesLoaded(container) {
        var imgs = Array.from(container.querySelectorAll('img'));
        if (imgs.length === 0) return Promise.resolve();
        return Promise.all(imgs.map(function(img){
            if (img.complete) return Promise.resolve();
            return new Promise(function(res){
                img.onload = res;
                img.onerror = res;
            });
        }));
    }

    return fetch('/css/productos-styles.css', {cache: 'no-store'})
        .then(function(response){
            if (!response.ok) throw new Error('CSS no disponible');
            return response.text();
        })
        .then(function(cssText){
            var extra = ' .productos-header h2{margin:0 0 8px;font-size:18px} table th, table td{border:1px solid #ddd;padding:6px;font-size:12px} table thead th{background:#f7f7f7}';
            var style = document.createElement('style');
            style.type = 'text/css';
            style.appendChild(document.createTextNode(cssText + extra));
            temp.appendChild(style);

            var content = document.createElement('div');
            content.innerHTML = bodyHtml;
            temp.appendChild(content);

            return Promise.all([
                document.fonts ? document.fonts.ready : Promise.resolve(),
                waitImagesLoaded(temp)
            ]);
        })
        .then(function(){
            return html2canvas(temp, { scale: 2, useCORS: true, allowTaint: true });
        })
        .then(function(canvas){
            var jsPDF = window.jspdf.jsPDF;
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

            return pdf.output('blob');
        })
        .finally(function(){
            if (temp && temp.parentNode) document.body.removeChild(temp);
        });
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
