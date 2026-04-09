$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $("#gridVentas").bootstrapTable({
        url: "/gridVentas",
        classes: "table-striped",
        method: "post",
        contentType: "application/x-www-form-urlencoded",
        pagination: true,
        pageSize: 10,
        search: true,
        // detailView: true,
        queryParams: function (p) {
            return {
                cTipoBusqueda: $("#cTipoBusquedaVenta").val(),
            };
        },
        columns: [{
            field: "id",
            title: "id",
            visible: false
        }, {
            field: "cNombreVenta",
            title: "Nombre de la venta",
            formatter: "nombreVentaFormatter",
        }, {
            field: "created_at",
            title: "Fecha",
            formatter: "fechaFormatter",
        }, {
            field: "nombre",
            title: "Cliente",
        }, {
            field: "total",
            title: "Total",
            formatter: "totalFormatter",
        }, {
            field: "tipo_pago",
            title: "Tipo de pago",
            formatter: "tipoPagoFormatter",
        }, {
            field: "name",
            title: "Vendio",
        },{
            field: "acciones",
            title: "",
            width: 200,
            formatter: "accionesFormatter",
        },],
        onLoadSuccess: function (data) {

        },
        onExpandRow: function (index, row, $detail) {
        },
    });
});

function fechaFormatter(value, row) {
    var user = $("#userID").val()
    const fecha = new Date(row.created_at);

    // Extraer el día, mes y año
    const dia = fecha.getDate();
    const mes = fecha.getMonth() + 1; // Los meses en JavaScript son base 0, así que sumamos 1
    const anio = fecha.getFullYear();
    const horas = fecha.getHours();
    const minutos = fecha.getMinutes();
    const segundos = fecha.getSeconds();

    // Formatear la fecha en formato d/m/y
    const fechaFormateada = `${dia}/${mes}/${anio} ${horas}:${minutos}:${segundos}`;

    return fechaFormateada;
}

function nombreVentaFormatter(value, row) {
    if(row.cNombreVenta == null || row.cNombreVenta == ""){
        return "Detalle de venta #" + row.id;
    }else{
        return row.cNombreVenta;
    }
}

function totalFormatter(value, row) {
    total = Math.round(row.total * 100) / 100; 

    return "$" + total + ".00";
}

function tipoPagoFormatter(value, row) {
    var tipo = row.tipo_pago || 'EFECTIVO';

    if (tipo === 'MERCADO_PAGO') {
        return '<span class="badge badge-warning">MERCADO PAGO</span>';
    }

    if (tipo === 'ABONOS') {
        return '<span class="badge badge-info">ABONOS</span>';
    }

    return '<span class="badge badge-success">EFECTIVO</span>';
}


function accionesFormatter(value, row) {
    var user = $("#userID").val()
    html = ''

    html += ' <button type="button" style="margin-right: 2px;" class="btn btn-success" title="Visualizar venta" onclick="verVenta('+row.id+')"><i class="far fa-list-alt"></i></button>'
    if(user == 1){
    //    html += ' <button type="button" style="margin-right: 2px;" class="btn btn-info" title="Imprimir ticket" onclick="imprimirTicket('+row.id+')"><i class="fa fa-print"></i></button>'
       html += ' <button type="button" style="margin-right: 2px;" class="btn btn-light" title="Editar nombre de venta" onclick="editNombreVenta('+row.id+', \''+row.cNombreVenta+'\')"><i class="fa fa-edit"></i></button>';
    }
    // console.log(row.apartado_id_venta > 0)
    if(row.apartado_id_venta > 0){
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-secondary" title="Ver historial de abonos" onclick="verHistorialAbonos(' + row.apartado_id + ')"><i class="fas fa-history"></i></button>';
         html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" title="Descargar PDF Historial de abonos" onclick="descargarPdfApartado(' + row.apartado_id + ')"><i class="fas fa-file-pdf"></i></button>';
    }
    return html;
}

function imprimirTicket(id){
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
            console.error('Error generando PDF:', err);
            var w = window.open('', '_blank', 'width=400,height=600');
            if (!w) { alert('Permite ventanas emergentes para imprimir el ticket.'); return; }
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
// function imprimirTicket(id){
//     // Intentamos obtener datos en JSON desde el endpoint de ticket; si no responde con JSON, se mantiene el comportamiento antiguo (redirigir)
//     $.ajax({
//         url: "/ventas/ticket/" + id,
//         type: "get",
//         dataType: "json",
//         success: function (data) {
//             if (data && data.items) {
//                 // esperar a que printTicketVenta esté disponible
//                 if (typeof printTicketVenta === 'function') {
//                     printTicketVenta(data.items, data.total, data.fecha || '', data.usuario || '');
//                 } else {
//                     console.error('printTicketVenta no disponible');
//                     window.location.href = "/ventas/ticket/" + id;
//                 }
//             } else {
//                 // respuesta no es JSON con los datos esperados -> fallback a la URL (posible PDF/HTML del servidor)
//                 window.location.href = "/ventas/ticket/" + id;
//             }
//         },
//         error: function () {
//             // fallback: redirigir a la URL tradicional
//             window.location.href = "/ventas/ticket/" + id;
//         }
//     });
// }

function verVenta(id){
    window.location.href = "/ventas/" + id;
}

function editNombreVenta(id, cNombreVenta){
    $("#iIDVenta").val(id);
    if(cNombreVenta == 'null' || cNombreVenta == ""){
        // var nom = "Detalle de venta #" + id;
        $("#cNombreVenta").val("Detalle de venta #" + id)
    }else{
        $("#cNombreVenta").val(cNombreVenta)
    }
    $("#editNombreVentaModal").modal("show")
}

$("#btnGuardarNombreVenta").on('click', function () {
    id = $("#iIDVenta").val();
    cNombreVenta = $("#cNombreVenta").val();
    saveNombreVenta(id, cNombreVenta);
});

function saveNombreVenta(id, cNombreVenta){
    $.ajax({
        url: "/saveNombreVenta",
        type: "post",
        dataType: "json",
        data: {
            id: id,
            cNombreVenta: cNombreVenta,
        },
        success: function (data) {
            if (data.lSuccess) {
                $("#editNombreVentaModal").modal('hide');
                swal.fire({
                    title: "Editar Venta",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridVentas").bootstrapTable('refresh');
            } else {
                swal.fire({
                    title: "Error",
                    text: data.cMensaje,
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
            }
        },
        error: function (data) {
            swal.fire({
                title: "Error",
                text: data.cMensaje,
                icon: "error",
                showConfirmButton: true,
                confirmButtonClass: "btn btn-success btn-round",
                confirmButtonText: "Aceptar",
                //buttonsStyling: false,
            });
        },
    });
}

$( "#cTipoBusquedaVenta" ).on( "change", function() {
    $("#gridVentas").bootstrapTable('refresh');
  } );

/* Imprimir ticket (mismo comportamiento que vender.js)
   Uso: printTicketVenta(itemsArray, total, fecha, usuario)
   itemsArray: array de objetos { codigo_barras, descripcion, cantidad, precio_venta }
*/
function printTicketVenta(items, total, fecha, usuario) {
    items = items || [];
    total = (total !== undefined) ? total : 0;
    fecha = fecha || '';
    usuario = usuario || '';

    // Construir markup de tabla (usa clases iguales a productos.index)
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

    // crear contenedor temporal
    var temp = document.createElement('div');
    temp.style.position = 'fixed';
    temp.style.left = '-9999px';
    temp.style.top = '0';
    temp.style.width = '800px'; // ancho para render de alta calidad
    document.body.appendChild(temp);

    // cargar CSS local (productos-styles.css) e inyectar en temp (inline) para html2canvas
    fetch('/css/productos-styles.css', {cache: "no-store"})
        .then(function(response){
            if (!response.ok) throw new Error('CSS no disponible');
            return response.text();
        })
        .then(function(cssText){
            // añadir estilo base para ticket (garantizar tabla visible)
            var extra = '\
                .productos-header h2{margin:0 0 8px;font-size:18px} \
                table th, table td{border:1px solid #ddd;padding:6px;font-size:12px} \
                table thead th{background:#f7f7f7}';
            var style = document.createElement('style');
            style.type = 'text/css';
            style.appendChild(document.createTextNode(cssText + extra));
            temp.appendChild(style);

            // insertar markup
            var content = document.createElement('div');
            content.innerHTML = bodyHtml;
            temp.appendChild(content);

            // esperar a que fuentes e imágenes (si tuviera) carguen
            return Promise.all([
                document.fonts ? document.fonts.ready : Promise.resolve(),
                waitImagesLoaded(temp)
            ]);
        })
        .then(function(){
            // generar canvas y PDF
            return html2canvas(temp, { scale: 2, useCORS: true, allowTaint: true });
        })
        .then(function(canvas){
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
                // dividir en páginas
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

            // abrir PDF en nueva pestaña (no imprimir)
            var blob = pdf.output('blob');
            var url = URL.createObjectURL(blob);
            var win = window.open(url, '_blank');
            if (!win) {
                // popup bloqueado -> descargar como fallback
                var a = document.createElement('a');
                a.href = url;
                a.download = 'ticket_' + Date.now() + '.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                setTimeout(function(){ URL.revokeObjectURL(url); }, 60000);
            }
        })
        .catch(function(err){
            console.error('Error generando PDF:', err);
            alert('No se pudo generar el PDF. Revisa la consola.');
        })
        .finally(function(){
            if (temp && temp.parentNode) document.body.removeChild(temp);
        });

    // helper: espera que imágenes dentro del contenedor se carguen
    function waitImagesLoaded(container) {
        var imgs = Array.from(container.querySelectorAll('img'));
        if (imgs.length === 0) return Promise.resolve();
        return Promise.all(imgs.map(function(img){
            if (img.complete) return Promise.resolve();
            return new Promise(function(res, rej){
                img.onload = res;
                img.onerror = res;
            });
        }));
    }
}

// Exponer en window si es necesario
window.printTicketVenta = printTicketVenta;

function verHistorialAbonos(id) {
    $.ajax({
        url: "/apartados/ver-abonos/" + id,
        type: "get",
        success: function (html) {
            $("#historialAbonosBody").html(html);
            $("#modalHistorialAbonos").modal("show");
        },
        error: function () {
            swal.fire({
                title: "Error",
                text: "No se pudo cargar el historial de abonos.",
                icon: "error",
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        }
    });
}

function obtenerFechaHoyLocal() {
    var ahora = new Date();
    var offset = ahora.getTimezoneOffset();
    var fechaLocal = new Date(ahora.getTime() - (offset * 60000));
    return fechaLocal.toISOString().slice(0, 10);
}

function editarFechaAbono(idAbono, idApartado, fechaActual) {
    swal.fire({
        title: "Editar fecha de abono",
        input: "date",
        inputValue: fechaActual || obtenerFechaHoyLocal(),
        showCancelButton: true,
        confirmButtonText: "Guardar",
        cancelButtonText: "Cancelar",
        inputAttributes: {
            max: obtenerFechaHoyLocal(),
        },
        inputValidator: (value) => {
            if (!value) {
                return "Debes seleccionar una fecha";
            }
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: "/apartados/editar-fecha-abono",
            type: "post",
            dataType: "json",
            data: {
                id_abono: idAbono,
                fecha_abono: result.value,
            },
            success: function (data) {
                if (data.lSuccess) {
                    swal.fire({
                        title: "Apartados",
                        text: data.cMensaje,
                        icon: "success",
                        showConfirmButton: true,
                        confirmButtonText: "Aceptar",
                    });
                    verHistorialAbonos(idApartado);
                    if ($("#gridVentas").length) {
                        $("#gridVentas").bootstrapTable("refresh");
                    }
                    if ($("#gridApartados").length) {
                        $("#gridApartados").bootstrapTable("refresh");
                    }
                } else {
                    swal.fire({
                        title: "Error",
                        text: data.cMensaje,
                        icon: "error",
                        showConfirmButton: true,
                        confirmButtonText: "Aceptar",
                    });
                }
            },
            error: function () {
                swal.fire({
                    title: "Error",
                    text: "Ocurrió un error al editar la fecha del abono.",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
            },
        });
    });
}

function descargarPdfApartado(id) {
    window.open('/apartados/pdf/' + id, '_blank');
}