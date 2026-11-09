@extends('maestra_api')
@section('titulo', 'Catálogo')
@section('contenido')

<div class="catalog-container">
    <!-- Header -->
    <div class="catalog-header">
        <div class="header-content">
            <span class="boutique-eyebrow">{{ config('app.name') }} · La colección</span>
            <div class="catalog-title">
                <h1>Encuentra una pieza<br><em>tan especial como tú.</em></h1>
            </div>
            <p class="catalog-subtitle">Pequeños detalles que dicen mucho. Explora nuestra selección y descubre tu próxima joya.</p>
            <div class="catalog-stats">
                <span class="catalog-stat-pill">{{ is_array($lstProducto) ? count($lstProducto) : $lstProducto->count() }} piezas por descubrir</span>
                <span class="catalog-stat-pill">{{ is_array($lstCategorias) ? count($lstCategorias) : $lstCategorias->count() }} categorías</span>
            </div>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div class="search-controls">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input id="catalogSearch" type="text" placeholder="Buscar por nombre, código o material..." aria-label="Buscar productos">
        </div>
        <select id="catalogSort" aria-label="Ordenar catálogo">
            <option value="popular">Selección de la casa</option>
            <option value="new">Nuevos primero</option>
            <option value="price_asc">Precio: menor a mayor</option>
            <option value="price_desc">Precio: mayor a menor</option>
        </select>
        <button type="button" id="btnClearCatalogFilters" class="btn-clear-filters">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </div>

    <!-- Categorías -->
    <div class="categories-section">
        <div class="categories-wrapper">
            <button class="cat-pill active" data-cat="all">Todos</button>
            @foreach($lstCategorias as $categoria)
                <button class="cat-pill" data-cat="{{ $categoria->id }}">
                    {{ $categoria->cNombreCategoria }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Grid de productos -->
    <div class="products-grid" id="productsGrid">
        @foreach($lstProducto as $index => $producto)
            <div class="jewel-card"
                 data-index="{{ $index }}"
                 data-categoria="{{ $producto->id_categoria }}"
                 data-material="{{ $producto->cNombreMaterial ?? '' }}"
                 data-title="{{ strtolower($producto->descripcion ?? '') }}"
                 data-product-name="{{ $producto->descripcion ?? '' }}"
                 data-code="{{ strtolower($producto->codigo_barras ?? '') }}"
                 data-price="{{ $producto->precio_venta ?? 0 }}">
                <button type="button" class="jewel-media" aria-label="Ver pieza: {{ $producto->descripcion }}" aria-haspopup="dialog" aria-controls="catalogImageModal">
                    @if($producto->img)
                        <img src="{{ asset('img/productos/'.$producto->img) }}" alt="{{ $producto->descripcion }}" loading="lazy">
                    @else
                        <img src="{{ asset('img/logo.jpg') }}" alt="imagen" loading="lazy">
                    @endif
                    @if($producto->es_nuevo ?? false)
                        <div class="jewel-badge">Nuevo</div>
                    @endif
                </button>
                <div class="jewel-body">
                    <div class="jewel-material">
                        <i class="fas fa-certificate"></i>
                        {{ $producto->cNombreMaterial ?? 'N/A' }}
                    </div>
                    <h3 class="jewel-title">{{ \Str::limit($producto->descripcion, 60) }}</h3>
                    <div class="jewel-meta">
                        <div class="jewel-code">Ref: <strong>{{ $producto->codigo_barras ?? '—' }}</strong></div>
                        <div class="jewel-price">@if($producto->precio_venta > 0)${{ number_format($producto->precio_venta, 2) }}@else<span class="price-on-request">Consultar precio</span>@endif</div>
                    </div>
                    <div class="jewel-actions">
                        <button class="btn-view" 
                                data-img="@if($producto->img){{ asset('img/productos/'.$producto->img) }}@else{{ asset('img/logo.jpg') }}@endif" 
                                data-title="{{ $producto->descripcion }}"
                                data-material="{{ $producto->cNombreMaterial ?? 'N/A' }}"
                                data-categoria="{{ $producto->cNombreCategoria ?? 'N/A' }}"
                                data-price="{{ number_format($producto->precio_venta, 2) }}">
                            Ver pieza <span aria-hidden="true">↗</span>
                        </button>
                        <button class="btn-whatsapp" @if(!$telefonoWhatsapp) disabled title="Contacto de WhatsApp no disponible" @endif data-title="{{ $producto->descripcion }}">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Botón Cargar Más -->
    <div class="text-center my-4" style="display: flex; justify-content: center; margin-bottom: 2rem;">
        <button type="button" id="btnLoadMore" class="btn" style="background: linear-gradient(135deg, var(--dark) 0%, var(--dark-secondary) 100%); color: var(--gold); border: 2px solid var(--gold); border-radius: 12px; font-weight: 700; padding: 0.9rem 2.5rem; display: none; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            Descubrir más piezas <span aria-hidden="true">+</span>
        </button>
    </div>

    <div id="catalogEmptyState" class="empty-state">
        <i class="fas fa-search"></i>
        <div style="font-weight:700; color:#2a2a2a;">No encontramos productos con esos filtros</div>
        <div style="margin-top:.25rem;">Prueba con otro texto, categoría o tipo de ordenamiento.</div>
    </div>

    <!-- Modal detalle -->
    <div class="modal fade" id="catalogImageModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-layout">
                        <img id="modalImage" class="modal-img" src="" alt="producto">
                        <div class="modal-info">
                            <h4 id="modalTitle"></h4>
                            <p id="modalMaterial" style="color: var(--gold); font-weight: 700; font-size: 1.1rem;">
                                <i class="fas fa-certificate"></i> <span></span>
                            </p>
                            <p id="modalCategoria" style="color: #666; font-weight: 600;">
                                <i class="fas fa-tag"></i> <span></span>
                            </p>
                            <p id="modalPrice"></p>
                            <div class="modal-actions">
                                <button type="button" class="catalog-modal-button catalog-modal-close" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cerrar
                                </button>
                                <button id="modalWhatsapp" type="button" class="catalog-modal-button catalog-modal-whatsapp" @if(!$telefonoWhatsapp) disabled title="Contacto de WhatsApp no disponible" @endif>
                                    <i class="fab fa-whatsapp"></i> Enviar WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(function(){
        var visibleLimit = 12;

        function applyCatalogFilters(resetLimit){
            if (resetLimit === true) {
                visibleLimit = 12;
            }

            var q = ($('#catalogSearch').val() || '').toLowerCase().trim();
            var activeCat = $('.cat-pill.active').data('cat');
            var sort = $('#catalogSort').val();
            
            var cards = $('#productsGrid .jewel-card').get();
            var matchedCards = [];

            cards.forEach(function(el){
                var $el = $(el);
                var title = ($el.data('title') || '').toString();
                var code = ($el.data('code') || '').toString();
                var material = ($el.data('material') || '').toString().toLowerCase();
                var inSearch = !q || title.indexOf(q) !== -1 || code.indexOf(q) !== -1 || material.indexOf(q) !== -1;
                var inCategory = (activeCat === 'all') || ($el.data('categoria') == activeCat);
                
                if (inSearch && inCategory) {
                    matchedCards.push(el);
                } else {
                    $el.hide();
                }
            });

            // Ordenamiento de los elementos que coinciden
            matchedCards.sort(function(a, b){
                var $a = $(a), $b = $(b);
                var priceA = parseFloat($a.data('price')) || 0;
                var priceB = parseFloat($b.data('price')) || 0;
                var indexA = parseInt($a.data('index')) || 0;
                var indexB = parseInt($b.data('index')) || 0;

                if(sort === 'price_asc') return priceA - priceB;
                if(sort === 'price_desc') return priceB - priceA;
                if(sort === 'new') return indexB - indexA;
                return indexA - indexB;
            });

            // Mostrar solo hasta el límite visible y re-ordenar en el grid
            matchedCards.forEach(function(card, index){
                var $card = $(card);
                $('#productsGrid').append(card);
                if (index < visibleLimit) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });

            // Mensaje vacío
            $('#catalogEmptyState').toggle(matchedCards.length === 0);

            // Botón Cargar Más
            if (matchedCards.length > visibleLimit) {
                $('#btnLoadMore').show();
            } else {
                $('#btnLoadMore').hide();
            }
        }

        $('#catalogSearch').on('input', function() {
            applyCatalogFilters(true);
        });
        $('#catalogSort').on('change', function() {
            applyCatalogFilters(true);
        });

        $('.cat-pill').on('click', function(){
            $('.cat-pill').removeClass('active');
            $(this).addClass('active');
            applyCatalogFilters(true);
        });

        $('#btnClearCatalogFilters').on('click', function(){
            $('#catalogSearch').val('');
            $('#catalogSort').val('popular');
            $('.cat-pill').removeClass('active');
            $('.cat-pill[data-cat="all"]').addClass('active');
            applyCatalogFilters(true);
        });

        $('#btnLoadMore').on('click', function(){
            visibleLimit += 12;
            applyCatalogFilters(false);
        });

        var busquedaInicial = new URLSearchParams(window.location.search).get('buscar') || '';
        $('#catalogSearch').val(busquedaInicial);
        applyCatalogFilters(true);

        // Modal ver detalle
        $('.jewel-media').on('click', function(){
            $(this).closest('.jewel-card').find('.btn-view').trigger('click');
        });
        $('.btn-view').on('click', function(){
            var img = $(this).data('img');
            var title = $(this).data('title');
            var material = $(this).data('material');
            var categoria = $(this).data('categoria');
            var price = $(this).data('price');
            var code = $(this).closest('.jewel-card').data('code') || '';
            
            $('#modalImage').attr('src', img);
            $('#modalTitle').text(title);
            $('#modalMaterial span').text(material);
            $('#modalCategoria span').text(categoria);
            // Formatear precio
            var rawPrice = price.toString().replace(/,/g, '');
            $('#modalPrice').text(parseFloat(rawPrice) > 0 ? '$' + parseFloat(rawPrice).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : 'Consulta el precio de esta pieza');

            // Populate modalWhatsapp data attributes
            $('#modalWhatsapp')
                .data('title', title)
                .data('material', material)
                .data('img', img)
                .data('code', code)
                .data('price', rawPrice)
                .data('categoria', categoria);

            // Evento de Google Analytics 4 (Ver detalle del producto)
            if (typeof gtag === 'function') {
                gtag('event', 'view_product_detail', {
                    'product_code': code,
                    'product_name': title,
                    'product_material': material,
                    'product_category': categoria,
                    'product_price': rawPrice
                });
            }

            $('#catalogImageModal').modal('show');
        });

        // WhatsApp rápido
        $('.btn-whatsapp, #modalWhatsapp').on('click', function(){
            var title = '';
            var material = '';
            var code = '';
            var price = '';
            var categoria = '';
            var isModal = $(this).attr('id') === 'modalWhatsapp';

            if (isModal) {
                title = $(this).data('title') || '';
                material = $(this).data('material') || '';
                code = $(this).data('code') || '';
                price = $(this).data('price') || '';
                categoria = $(this).data('categoria') || '';
            } else {
                var $card = $(this).closest('.jewel-card');
                title = $card.attr('data-product-name') || $card.find('.jewel-title').text().trim();
                code = $card.data('code') || '';
                material = $card.data('material') || '';
                price = $card.data('price') || '';
                categoria = $card.data('categoria') || '';
            }

            // Normalización de material
            if (material) {
                material = material.toString().toUpperCase().replace('N/A', '').trim();
            }

            // Evento de Google Analytics 4 (Click en WhatsApp)
            if (typeof gtag === 'function') {
                gtag('event', 'click_whatsapp_contact', {
                    'product_code': code,
                    'product_name': title,
                    'product_material': material,
                    'product_category': categoria,
                    'product_price': price,
                    'click_source': isModal ? 'modal' : 'grid'
                });
            }

            var text = '¡Hola! Me interesa esta pieza de su catálogo:\n\n' +
                       '*' + title + '*\n';
            if (code) {
                text += 'Referencia: ' + code.toString().toUpperCase() + '\n';
            }
            if (material) {
                text += 'Material: ' + material + '\n';
            }
            if (parseFloat(price) > 0) {
                text += 'Precio en catálogo: $' + parseFloat(price).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '\n';
            }
            var enlaceProducto = new URL(window.location.pathname, window.location.origin);
            enlaceProducto.searchParams.set('buscar', title);
            text += '\nVer en el catálogo:\n' + enlaceProducto.href + '\n';
            text += '\n¿Me podrían confirmar su disponibilidad y orientarme para comprarla? ¡Gracias!';

            var phone = @json($telefonoWhatsapp);
            if (!phone) return;
            var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(text);
            window.open(url, '_blank', 'noopener,noreferrer');
        });
    });
</script>

@endsection
