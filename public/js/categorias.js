$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    tinymce.init({
        license_key: 'gpl', // Licencia de uso libre
        selector: 'textarea#cNotasCategoria',
        language: 'es_MX',
        height: 250,
        promotion: false // Oculta la promoción para volverlo premium
    });

    $("#gridCategorias").bootstrapTable({
        url: "/gridCategorias",
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
            field: "cNombreCategoria",
            title: "Nombre",
        },{
            field: "cNotasCategoria",
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
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editCategoria('+row.id+')"><i class="fa fa-edit"></i></button>'
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteCategoria(' + row.id + ', \'' + row.cNombreCategoria + '\')"><i class="fa fa-trash"></i></button>'
    }
    return html;
}

$("#btnAgregarCategoria").on('click', function () {
    $("#iIDCategoria").val(0)
    $("#cNombreCategoria").val("")
    $("#tituloFormCategorias").html("Agregar Categoria")
    $("#formCategoriaModal").modal('show');
});

$("#btnGuardarCategoria").on('click', function () {
    cNombreCategoria = $("#cNombreCategoria").val();
    cNotasCategoria = tinymce.get('cNotasCategoria');
    saveCategoria(cNombreCategoria, cNotasCategoria.getContent());
});

function saveCategoria(cNombreCategoria, cNotasCategoria){
    $.ajax({
        url: "/saveCategorias",
        type: "post",
        dataType: "json",
        data: {
            id: $("#iIDCategoria").val(),
            cNombreCategoria: cNombreCategoria,
            cNotasCategoria: cNotasCategoria,
        },
        success: function (data) {
            if (data.lSuccess) {
                $("#formCategoriaModal").modal('hide');
                swal.fire({
                    title: "Agregar Categoria",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridCategorias").bootstrapTable('refresh');
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

function editCategoria(id){
    $.ajax({
        url: "/getCategoria/"+id,
        type: "get",
        dataType: "json",
        success: function (data) {
            if (data.lSuccess) {
                $("#iIDCategoria").val(id)
                $("#cNombreCategoria").val(data.cData.cNombreCategoria);
                tinymce.get('cNotasCategoria').setContent(data.cData.cNotasCategoria);
                $("#tituloFormCategorias").html("Editar Categoria")
                $("#formCategoriaModal").modal('show');
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

function confirmDeleteCategoria(id, cNombreCategoria) {
    swal.fire({
        title: "Eliminar Categoria",
        text: "¿Desea eliminar la categoria "+cNombreCategoria+"?",
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
                deleteCategoria(id);
            }
        }, () => { });
}

function deleteCategoria(id){
    $.ajax({
        url: "/deleteCategoria",
        type: "post",
        dataType: "json",
        data: {
            id: id,
        },
        success: function (data) {
            if (data.lSuccess) {
                swal.fire({
                    title: "Eliminar Categoria",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridCategorias").bootstrapTable('refresh');
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