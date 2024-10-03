$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });

    $("#gridEstadisticas").bootstrapTable({
        url: "/gridEstadisticas",
        classes: "table-striped",
        method: "post",
        contentType: "application/x-www-form-urlencoded",
        pagination: true,
        pageSize: 10,
        search: true,
        // detailView: true,
        queryParams: function (p) {
            return {
                cTipoBusqueda: $("#cTipoBusquedaProductos").val(),
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