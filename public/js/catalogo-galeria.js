(function () {
    'use strict';
    window.mostrarGaleriaCatalogo = function (principal, adicionales, descripcion) {
        var imagenes = [principal].concat(Array.isArray(adicionales) ? adicionales : []).filter(function (src, index, todas) { return src && todas.indexOf(src) === index; });
        var contenedor = $('#catalogGalleryThumbs').empty();
        var contador = $('#catalogGalleryCounter');
        function seleccionar(index) {
            $('#modalImage').attr('src', imagenes[index]).attr('alt', descripcion || 'Imagen de la pieza');
            contador.text((index + 1) + ' / ' + imagenes.length).prop('hidden', imagenes.length < 2);
            contenedor.find('button').each(function (position) { $(this).toggleClass('active', position === index).attr('aria-pressed', String(position === index)); });
        }
        if (imagenes.length > 1) imagenes.forEach(function (src, index) {
            $('<button>', { type: 'button', 'aria-label': 'Ver imagen ' + (index + 1), 'aria-pressed': 'false' })
                .append($('<img>', { src: src, alt: '', loading: 'lazy' })).on('click', function () { seleccionar(index); }).appendTo(contenedor);
        });
        if (imagenes.length) seleccionar(0);
        else contador.prop('hidden', true);
    };
}());
