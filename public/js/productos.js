var url_logo = "{{ url('/img/productos/') }}";

    document.addEventListener('DOMContentLoaded', function() {
        // Vista previa de imagen
        const imgInput = document.getElementById('img');
        const previewContainer = document.getElementById('preview-container');

        imgInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'La imagen debe ser menor a 2MB',
                        icon: 'warning',
                        confirmButtonColor: '#D4AF37'
                    });
                    imgInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
                }
                reader.readAsDataURL(file);
            }
        });

        // Cálculo de margen de ganancia
        const precioCompra = document.getElementById('precio_compra');
        const precioVenta = document.getElementById('precio_venta');
        const profitInfo = document.getElementById('profit-info');
        const profitMargin = document.getElementById('profit-margin');
        const profitAmount = document.getElementById('profit-amount');

        function calcularGanancia() {
            const compra = parseFloat(precioCompra.value) || 0;
            const venta = parseFloat(precioVenta.value) || 0;

            if (compra > 0 && venta > 0) {
                const ganancia = venta - compra;
                const margen = ((ganancia / compra) * 100).toFixed(2);
                
                profitMargin.textContent = margen + '%';
                profitAmount.textContent = ganancia.toFixed(2);
                profitInfo.style.display = 'flex';
                
                if (ganancia > 0) {
                    profitInfo.className = 'profit-indicator profit-positive';
                } else {
                    profitInfo.className = 'profit-indicator profit-negative';
                }
            } else {
                profitInfo.style.display = 'none';
            }
        }

        precioCompra.addEventListener('input', calcularGanancia);
        precioVenta.addEventListener('input', calcularGanancia);

        // Validación del formulario
        document.getElementById('formProducto').addEventListener('submit', function(e) {
            const compra = parseFloat(precioCompra.value) || 0;
            const venta = parseFloat(precioVenta.value) || 0;

            if (venta < compra) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Confirmar?',
                    text: 'El precio de venta es menor al precio de compra',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D4AF37',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Continuar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('formProducto').submit();
                    }
                });
            }
        });
    });
    
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
            visible: $("#userID").val() == 1 ? true : false,
        }, {
            field: "precio_venta",
            title: "Precio Venta",
        }, {
            field: "precio_compra",
            title: "Utilidad",
            formatter: "utilidadFormatter",
            visible: $("#userID").val() == 1 ? true : false,
        }, {
            field: "existencia",
            title: "Existencia",
        }, {
            field: "proveedor",
            title: "Proveedor",
            visible: $("#userID").val() == 1 ? true : false,
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
                    classes: 'table-danger',
                    css: {
                        'border-left': '4px solid #e74c3c',
                        'background-color': '#ffebee',
                        'font-weight': '500',
                        'box-shadow': 'inset 0 1px 3px rgba(231, 76, 60, 0.1)',
                        'color': '#000' // texto en negro
                    }
                };
            } else if (row.existencia > 0 && row.existencia < 4) {
                return {
                    classes: 'table-warning',
                    css: {
                        'border-left': '4px solid #f39c12',
                        'background-color': '#fff9c4',
                        'font-weight': '500',
                        'box-shadow': 'inset 0 1px 3px rgba(243, 156, 18, 0.1)',
                        'color': '#000' // texto en negro
                    }
                };
            }
            return {};
        },
        onLoadSuccess: function (data) {

        },
        onExpandRow: function (index, row, $detail) {
        },
    });

    
$("#btnExportExcel").off('click').on('click', function() {
    try {
        if (typeof $('#gridProductos').bootstrapTable('exportTable') === 'function') {
            $('#gridProductos').bootstrapTable('exportTable', {
                type: 'excel',
                fileName: 'productos_' + new Date().getTime()
            });
            return;
        }
    } catch (e) {
        // fallback to HTML-based Excel
    }
    exportToExcelHtml('productos_' + new Date().getTime());
});

$("#btnExportPDF").off('click').on('click', function() {
    // open printable window with images; user can print/save as PDF
    exportToPrintableWindow('Productos - Exportar (Imprimir a PDF)');
});
});

function utilidadFormatter(value, row) {
    var precio = row.precio_venta - row.precio_compra;
    return parseFloat(precio).toFixed(2);
}

function imagenFormatter(value, row) {
    html = ''
    if(row.img == null){
        html = `<div class="img-placeholder" title="Sin imagen disponible">
                    <i class="fas fa-image"></i>
                    <span>SIN FOTO</span>
                </div>`
    }else{
        html = `<div class="img-wrapper" onclick="verImagen('${row.img}', '${row.codigo_barras}')" title="Haz clic para ver en grande">
                    <img src="img/productos/${row.img}" alt="${row.img}" class="product-img">
                    <div class="img-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>`
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
        // if(row.existencia < 1){
            // html += '<button type="button" class="btn btn-table-action btn-warning" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            // html += '<button type="button" class="btn btn-table-action btn-light" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        // }else if(row.existencia > 0 && row.existencia < 4){
            html += '<button type="button" class="btn btn-table-action btn-light" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            html += '<button type="button" class="btn btn-table-action btn-danger" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        // }else{
            // html += '<button type="button" class="btn btn-table-action btn-warning" onclick="editProducto('+row.id+')"><i class="fa fa-edit"></i></button>'
            // html += '<button type="button" class="btn btn-table-action btn-danger" onclick="confirmDeleteProducto(' + row.id + ', \'' + row.codigo_barras + '\')"><i class="fa fa-trash"></i></button>'
        // }

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
    var src = window.location.origin + "/img/productos/" + img;
    var title = `Producto ${codigo_barras}`;

    var $container = $("#divImagenProductoModal");
    $container.empty();

    // crear imagen
    var imgEl = document.createElement('img');
    imgEl.src = src;
    imgEl.alt = codigo_barras;
    imgEl.onload = function(){
        // opcional: ajustar dialogo si quieres basarte en dimensiones naturales
        // no necesario si CSS ya limita tamaño
    };
    $container.append(imgEl);

    $("#tituloImagenModal").text(title);
    $("#imagenProductoModal").modal('show');
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

// helper para obtener columnas y datos visibles
function getTableExportData() {
    var cols = $('#gridProductos').bootstrapTable('getVisibleColumns').filter(function(c){ return c.field && c.title; });
    var data = $('#gridProductos').bootstrapTable('getData', {useCurrentPage: false, includeHidden: false}) || [];
    return { cols: cols, data: data };
}

function downloadCSV(filename) {
    var out = getTableExportData();
    var headers = out.cols.map(function(c){ return '"' + (c.title || c.field).replace(/"/g, '""') + '"'; }).join(',');
    var rows = out.data.map(function(r){
        return out.cols.map(function(c){
            var v = r[c.field];
            if (v === null || v === undefined) v = '';
            // eliminar tags html si los hay
            v = String(v).replace(/<[^>]*>/g, '').replace(/"/g, '""');
            return '"' + v + '"';
        }).join(',');
    }).join('\r\n');
    var csv = headers + '\r\n' + rows;
    var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    var link = document.createElement("a");
    var url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", filename + ".csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

/* Excel export: NO incluir imágenes, sólo el nombre de archivo si existe */
function exportToExcelHtml(filename) {
    var out = getTableExportData();
    var html = '<html><head><meta charset="utf-8"><title>' + filename + '</title>';
    html += '<style>table{border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style>';
    html += '</head><body>';
    html += '<table><thead><tr>';
    out.cols.forEach(function(c){ html += '<th>' + (c.title || c.field) + '</th>'; });
    html += '</tr></thead><tbody>';
    out.data.forEach(function(r){
        html += '<tr>';
        out.cols.forEach(function(c){
            var v = r[c.field];
            if (c.field === 'img') {
                // En Excel no mostramos la imagen; mostramos el nombre de archivo si existe
                html += '<td>' + (v ? String(v).replace(/<[^>]*>/g, '') : '') + '</td>';
            } else {
                if (v === null || v === undefined) v = '';
                v = String(v).replace(/<[^>]*>/g, '');
                html += '<td>' + v + '</td>';
            }
        });
        html += '</tr>';
    });
    html += '</tbody></table></body></html>';
    var blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    link.href = url;
    link.download = filename + '.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

/* PDF/Imprimir: mantener imágenes (sin cambios) */
function exportToPrintableWindow(title) {
    var out = getTableExportData();
    var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + title + '</title>';
    html += '<style>table{width:100%;border-collapse:collapse;font-family:Arial,Helvetica,sans-serif}th,td{border:1px solid #ccc;padding:8px;text-align:left}th{background:#f5f5f5}img{max-width:120px;height:auto}</style>';
    html += '</head><body>';
    html += '<h2>' + title + '</h2>';
    html += '<table><thead><tr>';
    out.cols.forEach(function(c){ html += '<th>' + (c.title || c.field) + '</th>'; });
    html += '</tr></thead><tbody>';
    out.data.forEach(function(r){
        html += '<tr>';
        out.cols.forEach(function(c){
            var v = r[c.field];
            if (c.field === 'img') {
                if (v) {
                    var src = window.location.origin + '/img/productos/' + v;
                    html += '<td><img src="' + src + '"/></td>';
                } else {
                    html += '<td></td>';
                }
            } else {
                if (v === null || v === undefined) v = '';
                v = String(v).replace(/<[^>]*>/g, '');
                html += '<td>' + v + '</td>';
            }
        });
        html += '</tr>';
    });
    html += '</tbody></table></body></html>';

    var w = window.open('', '_blank');
    w.document.open();
    w.document.write(html);
    w.document.close();
    // esperar a que cargue antes de imprimir
    setTimeout(function(){ w.focus(); w.print(); }, 500);
}
