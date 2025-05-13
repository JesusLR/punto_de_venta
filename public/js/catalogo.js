$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-Token": $("meta[name=csrf-token]").attr("content"),
        },
    });
});

function verCategoria(id_categoria, id_material, nombre){
    $("#divTituloContainerDos").html(nombre)
    $.ajax({
        url: "/api/verCategoria/" + id_categoria + '/' + id_material,
        type: "get",
        dataType: "json",
        beforeSend: function () {
            swal.fire({
                title: "Cargando...",
                text: "Por favor espera mientras se cargan los productos.",
                allowOutsideClick: false,
                didOpen: () => {
                    swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();
            if (data.lSuccess) {
                // cardPorducto = ''
                $("#containerUno").fadeOut(500, function () {
                    $("#containerDos").fadeIn(500);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                })
                cardPorducto = '<div class="row">'
                data.lstProducto.forEach(function (producto) {
                    imgSrc = producto.img ? baseAssetUrl + producto.img : defaultImg;
                    cardPorducto += '<div class="col-md-4 d-flex mb-4">'+
                                        '<div class="card producto-card flex-fill">';
                                            cardPorducto += '<img src="' +  imgSrc + '" class="card-img-top producto-img" alt="Imagen" onclick="verImagenCatalogo(\'' + producto.img + '\', \'' + producto.codigo_barras + '\')">';
                                            cardPorducto += '<div class="card-body">'+
                                                '<h3 class="card-title"> ' + producto.descripcion + ' </h3>'+
                                                '<h6 class=""> Codigo: ' + producto.codigo_barras + ' </h6>';
                                                cardPorducto += '<button type="button" class="btn btn-primary" onclick="verImagenCatalogo(\'' + producto.img + '\', \'' + producto.codigo_barras + '\')">Ver imagen</button>';
                                                // cardPorducto += '<button type="button" class="btn btn-primary" onclick="verImagenCatalogo(' + producto.img + '", "' + producto.codigo_barras + '")">Ver imagen</button>';
                                            cardPorducto += '</div>'+
                                        '</div>'+
                                    '</div>  ';
                                
                });
                cardPorducto += '</div>';

                $("#divProdutos").html(cardPorducto)
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
            Swal.close();
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

$("#btnRegresarCatalogo").on('click', function () {
    $("#containerDos").fadeOut(500, function () {
        $("#containerUno").fadeIn(500);
    })
});

function verImagenCatalogo(img, codigo_barras){
    imgSrc = img ? baseAssetUrl + img : defaultImg;
    html = `<img src="${imgSrc}" alt="Imagen">`
    title = `Producto ${codigo_barras}`

    $("#divimagenProductoCatalogoModal").html(html)
    $("#tituloImagenModal").html(title)
    $("#imagenProductoCatalogoModal").modal('show')
}