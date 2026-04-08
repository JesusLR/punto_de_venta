$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $('#cTipoBusquedaApartado').select2();
    $('#cEstadoApartado').select2();

    $("#gridApartados").bootstrapTable({
        url: "/gridApartados/gridApartados",
        classes: "table-striped",
        method: "post",
        contentType: "application/x-www-form-urlencoded",
        pagination: true,
        pageSize: 10,
        search: true,
        queryParams: function (p) {
            return {
                cTipoBusqueda: $("#cTipoBusquedaApartado").val(),
                cEstadoApartado: $("#cEstadoApartado").val(),
                // cFechaInicioApartado: $("#cFechaInicioApartado").val(),
                // cFechaFinApartado: $("#cFechaFinApartado").val(),
            };
        },
        columns: [{
            field: "id",
            title: "ID",
            visible: false
        }, {
            field: "id",
            title: "Nombre del apartado",
            formatter: "folioApartadoFormatter",
        }, {
            field: "cliente",
            title: "Cliente",
        }, {
            field: "total",
            title: "Total",
            formatter: "montoTotalApartadoFormatter",
        }, {
            field: "abonado",
            title: "Abonado",
            formatter: "montoAbonadoApartadoFormatter",
        }, {
            field: "saldo",
            title: "Saldo",
            formatter: "montoSaldoApartadoFormatter",
        }, {
            field: "estado",
            title: "Estado",
            formatter: "estadoApartadoFormatter",
        }, {
            field: "created_at",
            title: "Fecha",
            formatter: "fechaApartadoFormatter",
        }, {
            field: "acciones",
            title: "",
            width: 240,
            formatter: "accionesApartadoFormatter",
        }],
    });

    // configurarRangoSemanaFechas();

});

$("#cTipoBusquedaApartado").on("change", function () {
    $("#gridApartados").bootstrapTable("refresh");
});

// $("#cEstadoApartado, #cFechaInicioApartado, #cFechaFinApartado").on("change", function () {
//     limitarRangoSemana();
//     $("#gridApartados").bootstrapTable("refresh");
// });
$("#cEstadoApartado").on("change", function () {
    limitarRangoSemana();
    $("#gridApartados").bootstrapTable("refresh");
});

// function configurarRangoSemanaFechas() {
//     var hoy = obtenerFechaISO(new Date());
//     var haceSieteDias = new Date();
//     haceSieteDias.setDate(haceSieteDias.getDate() - 6);

//     $("#cFechaFinApartado").val(hoy);
//     $("#cFechaInicioApartado").val(obtenerFechaISO(haceSieteDias));
// }

// function limitarRangoSemana() {
//     var fechaInicio = $("#cFechaInicioApartado").val();
//     var fechaFin = $("#cFechaFinApartado").val();

//     if (!fechaInicio || !fechaFin) {
//         return;
//     }

//     var inicio = new Date(fechaInicio + "T00:00:00");
//     var fin = new Date(fechaFin + "T00:00:00");

//     if (inicio > fin) {
//         $("#cFechaFinApartado").val(fechaInicio);
//         return;
//     }

//     var milisegundosPorDia = 1000 * 60 * 60 * 24;
//     var diferenciaDias = Math.floor((fin - inicio) / milisegundosPorDia) + 1;

//     if (diferenciaDias > 7) {
//         var nuevoInicio = new Date(fin);
//         nuevoInicio.setDate(fin.getDate() - 6);
//         $("#cFechaInicioApartado").val(obtenerFechaISO(nuevoInicio));
//     }
// }

function obtenerFechaISO(fecha) {
    var anio = fecha.getFullYear();
    var mes = String(fecha.getMonth() + 1).padStart(2, "0");
    var dia = String(fecha.getDate()).padStart(2, "0");
    return anio + "-" + mes + "-" + dia;
}

function fechaApartadoFormatter(value, row) {
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

function folioApartadoFormatter(value, row) {
    if(row.nombre_apartado == null){
         return "Detalle de venta Apartado #" + row.id;
    }else{
        return row.nombre_apartado;
    }
   
}

function montoTotalApartadoFormatter(value, row) {
    return "$" + (Math.round(parseFloat(row.total) * 100) / 100).toFixed(2);
}

function montoAbonadoApartadoFormatter(value, row) {
    return "$" + (Math.round(parseFloat(row.abonado) * 100) / 100).toFixed(2);
}

function montoSaldoApartadoFormatter(value, row) {
    const saldo = Math.round(parseFloat(row.saldo) * 100) / 100;
    const color = saldo > 0 ? "#e74c3c" : "#27ae60";
    return '<strong style="color: ' + color + ';">$' + saldo.toFixed(2) + '</strong>';
}

function estadoApartadoFormatter(value, row) {
    if (row.estado === 'LIQUIDADO') {
        return '<span class="badge badge-success">LIQUIDADO</span>';
    }
    return '<span class="badge badge-warning">ABIERTO</span>';
}

function accionesApartadoFormatter(value, row) {
    let html = '';

    html += '<button type="button" style="margin-right: 2px;" class="btn btn-light" title="Cambiar nombre de apartado" onclick="abrirModalNombreApartado(' + row.id + ', \'' + escaparTexto(row.nombre_apartado) + '\', ' + row.id_cliente + ')"><i class="fas fa-pen"></i></button>';

    if (row.estado !== 'LIQUIDADO') {
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-primary" title="Abonar" onclick="abrirModalAbono(' + row.id + ', \'' + escaparTexto(row.cliente) + '\', ' + row.total + ', ' + row.abonado + ', ' + row.saldo + ')"><i class="fas fa-money-bill"></i></button>';
    }

    html += '<button type="button" style="margin-right: 2px;" class="btn btn-info" title="Ver productos" onclick="verProductosApartado(' + row.id + ')"><i class="fas fa-box"></i></button>';
    html += '<button type="button" style="margin-right: 2px;" class="btn btn-secondary" title="Ver historial de abonos" onclick="verHistorialAbonos(' + row.id + ')"><i class="fas fa-history"></i></button>';
    html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" title="Descargar PDF Historial de abonos" onclick="descargarPdfApartado(' + row.id + ')"><i class="fas fa-file-pdf"></i></button>';

    if (row.estado === 'LIQUIDADO') {
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-success" title="Ejecutar venta" onclick="ejecutarApartado(' + row.id + ')"><i class="fas fa-check"></i></button>';
    }

    return html;
}

function escaparTexto(texto) {
    return (texto || '').replace(/'/g, "\\'");
}

function abrirModalAbono(id, cliente, total, abonado, saldo) {
    $("#id_apartado_abono").val(id);
    $("#folio-abono").text("#" + id);
    $("#cliente-abono").text(cliente);
    $("#total-abono").text("$" + parseFloat(total).toFixed(2));
    $("#abonado-abono").text("$" + parseFloat(abonado).toFixed(2));
    $("#saldo-abono").html('$' + parseFloat(saldo).toFixed(2));
    $("#monto_abono").attr("max", saldo).val("");
    $("#max-abono").text("Máximo: $" + parseFloat(saldo).toFixed(2));
    $("#fecha_abono").val(obtenerFechaActual());
    $("#tipo_pago_abono").val("EFECTIVO");
    $("#observaciones_abono").val("");

    $("#modalAbono").modal("show");
}
function abrirModalNombreApartado(id, nombre, idCliente) {
    $("#id_apartado_nombre").val(id);
    $("#nombre-apartado").val(nombre);
    $("#cliente-apartado").val(idCliente);
    $("#modalNombreApartado").modal("show");
}

function obtenerFechaActual() {
    var ahora = new Date();
    var offset = ahora.getTimezoneOffset();
    var fechaLocal = new Date(ahora.getTime() - (offset * 60000));

    return fechaLocal.toISOString().slice(0, 10);
}

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

function eliminarAbono(idAbono, idApartado) {
    swal.fire({
        title: "¿Eliminar abono?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: "/apartados/eliminar-abono",
            type: "post",
            dataType: "json",
            data: {
                id_abono: idAbono,
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
                    $("#gridApartados").bootstrapTable("refresh");
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
                    text: "Ocurrió un error al eliminar el abono.",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
            },
        });
    });
}

function verProductosApartado(id) {
    $.ajax({
        url: "/apartados/ver-productos/" + id,
        type: "get",
        success: function (html) {
            $("#productosBody").html(html);
            // $('#id_producto_apartado').select2();
            $("#modalProductos").modal("show");
        },
        error: function () {
            swal.fire({
                title: "Error",
                text: "No se pudieron cargar los productos del apartado.",
                icon: "error",
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        }
    });
}

function agregarProductoAApartado() {
    var idApartado = $("#id_apartado_producto").val();
    var idProducto = $("#id_producto_apartado").val();
    var cantidad = $("#cantidad_producto_apartado").val();

    if (!idProducto) {
        swal.fire({
            title: "Apartados",
            text: "Selecciona un producto para agregar.",
            icon: "warning",
            showConfirmButton: true,
            confirmButtonText: "Aceptar",
        });
        return;
    }

    $.ajax({
        url: "/apartados/agregar-producto",
        type: "post",
        dataType: "json",
        data: {
            id_apartado: idApartado,
            id_producto: idProducto,
            cantidad: cantidad,
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
                verProductosApartado(idApartado);
                $("#gridApartados").bootstrapTable("refresh");
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
                text: "Ocurrió un error al agregar el producto al apartado.",
                icon: "error",
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        },
    });
}

function descargarPdfApartado(id) {
    window.open('/apartados/pdf/' + id, '_blank');
}

function ejecutarApartado(id) {
    swal.fire({
        title: "¿Ejecutar venta?",
        text: "El apartado se convertirá en venta final.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, ejecutar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: "/apartados/ejecutar",
            type: "post",
            dataType: "json",
            data: {
                id_apartado: id,
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
                    $("#gridApartados").bootstrapTable("refresh");
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
                    text: "Ocurrió un error al ejecutar la venta.",
                    icon: "error",
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
            },
        });
    });
}

$("#formAbono").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
        url: "/apartados/abonar",
        type: "post",
        dataType: "json",
        data: {
            id_apartado: $("#id_apartado_abono").val(),
            monto_abono: $("#monto_abono").val(),
            fecha_abono: $("#fecha_abono").val(),
            tipo_pago: $("#tipo_pago_abono").val(),
            observaciones: $("#observaciones_abono").val(),
        },
        success: function (data) {
            if (data.lSuccess) {
                $("#modalAbono").modal("hide");
                swal.fire({
                    title: "Apartados",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
                $("#gridApartados").bootstrapTable("refresh");
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
                text: "Ocurrió un error al registrar el abono.",
                icon: "error",
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        },
    });
});

$("#btnSaveNombreApartado").on("click", function (e) {
    e.preventDefault();
    $.ajax({
        url: "/apartados/cambiarNombre",  
        type: "post",
        dataType: "json",
        data: {
            id_apartado: $("#id_apartado_nombre").val(),
            nombre_apartado: $("#nombre-apartado").val(),
            id_cliente: $("#cliente-apartado").val(),
        },  
        success: function (data) {
            if (data.lSuccess) {
                $("#modalNombreApartado").modal("hide");
                swal.fire({
                    title: "Apartados",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonText: "Aceptar",
                });
                $("#gridApartados").bootstrapTable("refresh");
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
                text: "Ocurrió un error al cambiar el nombre del apartado.",
                icon: "error",
                showConfirmButton: true,
                confirmButtonText: "Aceptar",
            });
        },
    });
});
