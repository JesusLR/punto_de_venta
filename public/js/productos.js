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
        exportDataType: 'all',
        exportTypes: ['excel', 'pdf'],
        exportOptions: {
            fileName: function () {
              return 'Productos'
            }
          },
        queryParams: function (p) {
            return {
                cTipoBusqueda: $("#cTipoBusquedaProductos").val(),
                cTipoBusquedaProveedor: $("#cTipoBusquedaProveedor").val(),
                cTipoBusquedaMaterial: $("#cTipoBusquedaMaterial").val(),
                cTipoBusquedaCategoria: $("#cTipoBusquedaCategoria").val(),
            };
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
            field: "proveedor",
            title: "Proveedor",
            // formatter: "proveedorFormatter",
        }, {
            field: "material",
            title: "Material",
            // formatter: "materialFormatter",
        }, {
            field: "categoria",
            title: "Categoria",
            // formatter: "categoriaFormatter",
        }, {
            field: "img",
            title: "Imagen",
            formatter: "imagenFormatter",
        }, {
            field: "acciones",
            title: "",
            formatter: "accionesFormatter",
        },],
        rowStyle: function (row, index) {
            if (row.existencia < 1) {
                return {
                    classes: 'table-danger'
                };
            }else if(row.existencia > 0 && row.existencia < 4){
                return {
                    classes: 'table-warning'
                };
            }
            return {};
        },
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
        // html = html = `<img src="img/logo.jpg" alt="Sin imagen" width="100" height="100">`
    }else{
        html = `<img src="img/productos/${row.img}" alt="${row.img}" width="100" height="100" onclick="verImagen('${row.img}', '${row.codigo_barras}')">`
    }
    return html;
}

// function proveedorFormatter(value, row) {
//     html = ''
//     if(row.id_proveedor == 0){
//         html = 'N/A'
//     }else{
//         html = row.Proveedor
//     }
//     return html;
// }

function accionesFormatter(value, row) {
    var user = $("#userID").val()
    html = ''
    if(user == 1){
        if(row.existencia < 1){
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-light" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        }else if(row.existencia > 0 && row.existencia < 4){
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-light" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        }else{
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-warning" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            html += '<button type="button" style="margin-right: 2px;" class="btn btn-danger" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        }

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

$( "#cTipoBusquedaProductos" ).on( "change", function() {
    $("#gridProductos").bootstrapTable('refresh');
  } );

$( "#cTipoBusquedaProveedor" ).on( "change", function() {
    $("#gridProductos").bootstrapTable('refresh');
  } );

$( "#cTipoBusquedaMaterial" ).on( "change", function() {
    $("#gridProductos").bootstrapTable('refresh');
  } );

$( "#cTipoBusquedaCategoria" ).on( "change", function() {
    $("#gridProductos").bootstrapTable('refresh');
  } );

$("#btnCargaExcell").on('click', function () {
    document.getElementById("fileExcellProductos").value = "";
    $("#cargaExcellModal").modal('show');
});

$("#btnGuardarInfoExcell").on('click', function () {
    if ($("#fileExcellProductos").prop("files")[0] == null) {
        swal.fire({
            title: "Alerta",
            text: "Cargue un Excell.",
            icon: "warning",
            showConfirmButton: true,
            confirmButtonClass: "btn btn-success btn-round",
            confirmButtonText: "Aceptar",
            //buttonsStyling: false,
        });
        return false;
    }

    var data = new FormData();
    data.append("fileExcell", $("#fileExcellProductos").prop("files")[0]);

    $.ajax({
        url: "/cargarProductosExcell",
        type: "post",
        dataType: "json",
        data: data,
        async: true,
        cache: false,
        enctype: "multipart/form-data",
        contentType: false,
        processData: false,
        success: function (data) {
            if (data.lSuccess) {
                $("#cargaExcellModal").modal('hide');
                swal.fire({
                    title: "Carga de productos",
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
});

function verImagen(img, codigo_barras){
    html = `<img src="img/productos/${img}" alt="${img}">`
    title = `Producto ${codigo_barras}`

    $("#divImagenProductoModal").html(html)
    $("#tituloImagenModal").html(title)
    $("#imagenProductoModal").modal('show')
}

var $table = $('#table')

function idFormatter() {
  return 'Total'
}

function nameFormatter(data) {
  return data.length
}

function priceFormatter(data) {
  var field = this.field
  return '$' + data.map(function (row) {
    return +row[field].substring(1)
  }).reduce(function (sum, i) {
    return sum + i
  }, 0)
}