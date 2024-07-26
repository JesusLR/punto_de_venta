$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $("#gridProductos").bootstrapTable({
        url: "/gridProductos",
        classes: "table-striped",
        method: "post",
        contentType: "application/x-www-form-urlencoded",
        pagination: true,
        pageSize: 10,
        search: true,
        // detailView: true,
        queryParams: function (p) {
            return {
            };
        },
        icons: {
            detailOpen: "fas fa-plus",
            detailClose: "fas fa-minus",
        },
        columns: [{
            field: "id",
            title: "id",
            visible: false
        }, {
            field: "codigo_barras",
            title: "Codigo",
        }, {
            field: "descripcion",
            title: "Nombre",
        }, {
            field: "precio_compra",
            title: "Precio Compra",
        }, {
            field: "precio_venta",
            title: "Precio Venta",
        }, {
            field: "precio_compra",
            title: "Utilidad",
            formatter: "utilidadFormatter",
        }, {
            field: "existencia",
            title: "Existencia",
        }, {
            field: "img",
            title: "Imagen",
            formatter: "imagenFormatter",
        }, {
            field: "acciones",
            title: "",
            formatter: "accionesFormatter",
        },],
        onLoadSuccess: function (data) {

        },
        onExpandRow: function (index, row, $detail) {
        },
    });
});

function utilidadFormatter(value, row) {
    var precio = row.precio_venta - row.precio_compra;
    return parseFloat(precio).toFixed(2);
}

function imagenFormatter(value, row) {
    html = ''
    if(row.img == null){
        html = 'SIN FOTO'
    }else{
        html = '<img src="img/productos/'+row.img+'" alt="'+row.img+'" width="100" height="100">'
    }
    return html;
}

function accionesFormatter(value, row) {
    var user = $("#userID").val()
    html = ''
    if(user == 1){
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
    }
    return html;
}

function montoFormatter(value, row) {
    var html = ""
    html = parseFloat(row.dTotal).toFixed(2);
    return html;
}

function editProducto(id){
    window.location.href = "/editarProducto/"+id;
}

function confirmDeleteProducto(id, codigo_barras) {
    swal.fire({
        title: "Eliminar Producto",
        text: "¿Desea eliminar el producto "+codigo_barras+"?",
        icon: "warning",
        showCloseButton: false,
        showCancelButton: true,
        confirmButtonText: "Aceptar",
        confirmButtonColor: "#35A5E2",
        cancelButtonText: "Cancelar",
        cancelButtonColor: "#dc3545",
    }).then(
        (result) => {
            if (result.isConfirmed) {
                deleteProducto(id);
            }
        }, () => { });
}

function deleteProducto(id){
    $.ajax({
        url: "/deleteProducto",
        type: "post",
        dataType: "json",
        data: {
            id: id,
        },
        success: function (data) {
            if (data.lSuccess) {
                swal.fire({
                    title: "Eliminar Producto",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridProductos").bootstrapTable('refresh');
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
