$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    tinymce.init({
        license_key: 'gpl', // Licencia de uso libre
        selector: 'textarea#cNotasMaterial',
        language: 'es_MX',
        height: 250,
        promotion: false // Oculta la promoción para volverlo premium
    });

    $("#gridMateriales").bootstrapTable({
        url: "/gridMateriales",
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
            field: "cNombreMaterial",
            title: "Nombre",
        },{
            field: "cNotasMaterial",
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
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editMaterial('+row.id+')"><i class="fa fa-edit"></i></button>'
        html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteMaterial(' + row.id + ', \'' + row.cNombreMaterial + '\')"><i class="fa fa-trash"></i></button>'
    }
    return html;
}

$("#btnAgregarMaterial").on('click', function () {
    $("#iIDMaterial").val(0)
    $("#cNombreMaterial").val("")
    $("#tituloFormMateriales").html("Agregar Mateial")
    $("#formMaterialModal").modal('show');
});

$("#btnGuardarMaterial").on('click', function () {
    cNombreMaterial = $("#cNombreMaterial").val();
    cNotasMaterial = tinymce.get('cNotasMaterial');
    saveMaterial(cNombreMaterial, cNotasMaterial.getContent());
});

function saveMaterial(cNombreMaterial, cNotasMaterial){
    $.ajax({
        url: "/saveMateriales",
        type: "post",
        dataType: "json",
        data: {
            id: $("#iIDMaterial").val(),
            cNombreMaterial: cNombreMaterial,
            cNotasMaterial: cNotasMaterial,
        },
        success: function (data) {
            if (data.lSuccess) {
                $("#formMaterialModal").modal('hide');
                swal.fire({
                    title: "Agregar Material",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridMateriales").bootstrapTable('refresh');
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

function editMaterial(id){
    $.ajax({
        url: "/getMaterial/"+id,
        type: "get",
        dataType: "json",
        success: function (data) {
            if (data.lSuccess) {
                $("#iIDMaterial").val(id)
                $("#cNombreMaterial").val(data.cData.cNombreMaterial);
                tinymce.get('cNotasMaterial').setContent(data.cData.cNotasMaterial);
                $("#tituloFormMateriales").html("Editar Material")
                $("#formMaterialModal").modal('show');
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

function confirmDeleteMaterial(id, cNombreMaterial) {
    swal.fire({
        title: "Eliminar Material",
        text: "¿Desea eliminar el material "+cNombreMaterial+"?",
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
                deleteMaterial(id);
            }
        }, () => { });
}

function deleteMaterial(id){
    $.ajax({
        url: "/deleteMaterial",
        type: "post",
        dataType: "json",
        data: {
            id: id,
        },
        success: function (data) {
            if (data.lSuccess) {
                swal.fire({
                    title: "Eliminar Material",
                    text: data.cMensaje,
                    icon: "success",
                    showConfirmButton: true,
                    confirmButtonClass: "btn btn-success btn-round",
                    confirmButtonText: "Aceptar",
                    //buttonsStyling: false,
                });
                $("#gridMateriales").bootstrapTable('refresh');
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