$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    inputFechas();
    inicializarGridDetalle();
    cargarProductosFiltro();
    $("#cProducto").select2({
        width: '100%',
        // placeholder: 'Selecciona un producto',
        // allowClear: true
    });

    $("#btnBuscarEstadistica").on('click', function () {
        cargarDetalleProductos();
    });

    $(document).on('change', '#cFiltroUnidades', function () {
        toggleFechasPorFiltro();
        cargarDetalleProductos();
    });

    $(document).on('change', '#cProducto', function () {
        cargarDetalleProductos();
    });
});

function inicializarGridDetalle() {
    $('#gridDetalleVentas').bootstrapTable({
        pagination: true,
        sidePagination: 'client',
        pageSize: 10,
        pageList: [10, 25, 50, 100],
        search: true,
        locale: 'es-MX',
        undefinedText: '-',
        columns: [
            { field: 'fecha_movimiento', title: 'Fecha' },
            { field: 'codigo_barras', title: 'Código de barras' },
            { field: 'descripcion', title: 'Producto' },
            { field: 'total_unidades', title: 'Unidades vendidas', align: 'right', formatter: decimalFormatter },
            { field: 'precio_promedio', title: 'Precio promedio', align: 'right', formatter: moneyFormatter },
            { field: 'total_vendido', title: 'Total vendido', align: 'right', formatter: moneyFormatter }
        ]
    });
}

function inputFechas() {
    var hoy = new Date();
    var inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    var finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

    var ddInicio = String(inicioMes.getDate()).padStart(2, '0');
    var mmInicio = String(inicioMes.getMonth() + 1).padStart(2, '0');
    var yyyyInicio = inicioMes.getFullYear();

    var ddFin = String(finMes.getDate()).padStart(2, '0');
    var mmFin = String(finMes.getMonth() + 1).padStart(2, '0');
    var yyyyFin = finMes.getFullYear();

    $('#dtFechaInicio').val(yyyyInicio + '-' + mmInicio + '-' + ddInicio);
    $('#dtFechaFinal').val(yyyyFin + '-' + mmFin + '-' + ddFin);
}

function toggleFechasPorFiltro() {
    var esGeneral = $('#cFiltroUnidades').val() === 'GENERAL';
    $('#dtFechaInicio').prop('disabled', esGeneral);
    $('#dtFechaFinal').prop('disabled', esGeneral);
}

function cargarProductosFiltro() {
    $.ajax({
        url: '/estadisticas/productos-filtro',
        type: 'post',
        dataType: 'json',
        success: function (data) {
            var $select = $('#cProducto');
            $select.empty();
            $select.append('<option value="T">Todos</option>');

            if (data.lSuccess && data.productos) {
                data.productos.forEach(function (producto) {
                    var texto = (producto.codigo_barras || '-') + ' - ' + (producto.descripcion || '') + ' (Existencia: ' + (producto.existencia || 0) + ')';
                    $select.append('<option value="' + producto.codigo_barras + '">' + texto + '</option>');
                });
            }

            toggleFechasPorFiltro();
            cargarDetalleProductos();
        },
        error: function () {
            toggleFechasPorFiltro();
            cargarDetalleProductos();
        }
    });
}

function filtrosPayload() {
    return {
        dtFechaInicio: $('#dtFechaInicio').val(),
        dtFechaFinal: $('#dtFechaFinal').val(),
        cFiltroUnidades: $('#cFiltroUnidades').val() || 'RANGO',
        cProducto: $('#cProducto').val() || 'T'
    };
}

function cargarDetalleProductos() {
    $.ajax({
        url: '/gridEstadisticas',
        type: 'post',
        dataType: 'json',
        data: filtrosPayload(),
        success: function (data) {
            if (!data.lSuccess) {
                $('#gridDetalleVentas').bootstrapTable('load', []);
                actualizarResumen({ total_productos: 0, total_unidades: 0, total_vendido: 0 });
                return;
            }

            var filas = (data.productos || []).map(function (producto) {
                return {
                    fecha_movimiento: producto.fecha_movimiento ? formatoFechaSimple(producto.fecha_movimiento) : 'ACUMULADO',
                    codigo_barras: producto.codigo_barras || '-',
                    descripcion: producto.descripcion || '-',
                    total_unidades: parseFloat(producto.total_unidades || 0),
                    precio_promedio: parseFloat(producto.precio_promedio || 0),
                    total_vendido: parseFloat(producto.total_vendido || 0)
                };
            });

            $('#gridDetalleVentas').bootstrapTable('load', filas);
            actualizarResumen(data.resumen || { total_productos: 0, total_unidades: 0, total_vendido: 0 });
        },
        error: function () {
            $('#gridDetalleVentas').bootstrapTable('load', []);
            actualizarResumen({ total_productos: 0, total_unidades: 0, total_vendido: 0 });
        }
    });
}

function actualizarResumen(resumen) {
    var totalProductos = parseInt(resumen.total_productos || 0, 10);
    var totalUnidades = parseFloat(resumen.total_unidades || 0);
    var totalVendido = parseFloat(resumen.total_vendido || 0);

    $('#txtTotalProductos').text(totalProductos.toLocaleString('es-MX'));
    $('#txtTotalUnidades').text(totalUnidades.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    $('#txtTotalVendido').text('$' + totalVendido.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
}

function decimalFormatter(value) {
    return parseFloat(value || 0).toFixed(2);
}

function moneyFormatter(value) {
    return '$' + parseFloat(value || 0).toFixed(2);
}

function formatoFechaSimple(fechaValor) {
    var fecha = new Date(fechaValor + 'T00:00:00');
    if (isNaN(fecha.getTime())) {
        return fechaValor;
    }
    var dia = String(fecha.getDate()).padStart(2, '0');
    var mes = String(fecha.getMonth() + 1).padStart(2, '0');
    var anio = fecha.getFullYear();
    return dia + '/' + mes + '/' + anio;
}
