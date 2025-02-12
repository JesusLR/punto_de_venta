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


function accionesFormatter(value, row) {
    var user = $("#userID").val()
    html = ''

    html += ' <button type="button" style="margin-right: 2px;" class="btn btn-success" title="Visualizar venta" onclick="verVenta('+row.id+')"><i class="far fa-list-alt"></i></button>'
    if(user == 1){
       html += ' <button type="button" style="margin-right: 2px;" class="btn btn-info" title="Imprimir ticket" onclick="imprimirTicket('+row.id+')"><i class="fa fa-print"></i></button>'
       html += ' <button type="button" style="margin-right: 2px;" class="btn btn-warning" title="Editar nombre de venta" onclick="editNombreVenta('+row.id+', \''+row.cNombreVenta+'\')"><i class="fa fa-edit"></i></button>';
    }
    return html;
}

function imprimirTicket(id){
    window.location.href = "/ventas/ticket/" + id;
}

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