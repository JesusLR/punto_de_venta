$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    tinymce.init({
        license_key: 'gpl', // Licencia de uso libre
        selector: 'textarea#cNotasProveedor',
        language: 'es_MX',
        height: 250,
        promotion: false // Oculta la promoción para volverlo premium
    });

    $("#gridProveedores").bootstrapTable({
        url: "/gridProveedores",
        classes: "table-striped",
        method: "post",
        contentType: "application/x-www-form-urlencoded",
        pagination: true,
        pageSize: 10,
        search: true,
        // detailView: true,
        queryParams: function (p) {
            return {
                cTipoBusqueda: null,
            };
        },
        columns: [{
            field: "id",
            title: "id",
            visible: false
        }, {
            field: "cNombreProveedor",
            title: "Nombre",
        },{
            field: "cNotasProveedor",
            title: "Informacion",
        },{
            field: "acciones",
            title: "",
            width: "100px",
            formatter: "accionesFormatter",
        },],
        onLoadSuccess: function (data) {

        },
        onExpandRow: function (index, row, $detail) {
        },
    });
});

function accionesFormatter(value, row) {
    var user = $("#userID").val()
    html = ''
    if(user == 1){
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editProveedor('+row.id+')"><i class="fa fa-edit"></i></button>'
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteProveedor(' + row.id + ', \'' + row.cNombreProveedor + '\')"><i class="fa fa-trash"></i></button>'
    }
    return html;
}

$("#btnAgregarProveedor").on('click', function () {
    $("#iIDProveedor").val(0)
    $("#cNombreProveedor").val("")
    $("#tituloFormProveedores").html("Agregar Proveedor")
    $("#formProveedorModal").modal('show');
});

$("#btnGuardarProveedor").on('click', function () {
    cNombreProveedor = $("#cNombreProveedor").val();
    cNotasProveedor = tinymce.get('cNotasProveedor');
    saveProveedor(cNombreProveedor, cNotasProveedor.getContent());
});

function saveProveedor(cNombreProveedor, cNotasProveedor){
    $.ajax({
        url: "/saveProveedores",
        type: "post",
        dataType: "json",
        data: {
            id: $("#iIDProveedor").val(),
            cNombreProveedor: cNombreProveedor,
            cNotasProveedor: cNotasProveedor,
        },
        success: function (data) {
            if (data.lSuccess) {
                $("#formProveedorModal").modal('hide');
                swal.fire({
                    title: "Agregar Proveedor",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridProveedores").bootstrapTable('refresh');
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

function editProveedor(id){
    $.ajax({
        url: "/getProveedor/"+id,
        type: "get",
        dataType: "json",
        success: function (data) {
            if (data.lSuccess) {
                $("#iIDProveedor").val(id)
                $("#cNombreProveedor").val(data.cData.cNombreProveedor);
                tinymce.get('cNotasProveedor').setContent(data.cData.cNotasProveedor);
                $("#tituloFormProveedores").html("Editar Proveedor")
                $("#formProveedorModal").modal('show');
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

function confirmDeleteProveedor(id, cNombreProveedor) {
    swal.fire({
        title: "Eliminar Proveedor",
        text: "¿Desea eliminar al proveedor "+cNombreProveedor+"?",
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
                deleteProveedor(id);
            }
        }, () => { });
}

function deleteProveedor(id){
    $.ajax({
        url: "/deleteProveedor",
        type: "post",
        dataType: "json",
        data: {
            id: id,
        },
        success: function (data) {
            if (data.lSuccess) {
                swal.fire({
                    title: "Eliminar Proveedor",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridProveedores").bootstrapTable('refresh');
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