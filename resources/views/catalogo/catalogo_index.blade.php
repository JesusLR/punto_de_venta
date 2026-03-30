@extends('maestra_api')
@section('titulo', 'Catálogo')
@section('contenido')

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<style>
    /* Variables y reset */
    :root {
        --gold: #D4AF37;
        --gold-light: #F4D03F;
        --dark: #1a1a1a;
        --dark-secondary: #2d2d2d;
        --text-light: #f7f7f7;
        --bg-card: #ffffff;
        --shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .catalog-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Header elegante y moderno */
    .catalog-header {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-secondary) 100%);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
    }

    .catalog-header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .catalog-title {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .catalog-title h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--gold);
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .catalog-title i {
        font-size: 2.5rem;
        color: var(--gold);
    }

    .catalog-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
        font-weight: 300;
        letter-spacing: 0.5px;
    }

    /* Controles de búsqueda mejorados */
    .search-controls {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: center;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 1.1rem;
    }

    #catalogSearch {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    #catalogSearch:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 4px rgba(212,175,55,0.1);
    }

    #catalogSort {
        padding: 1rem 1.5rem;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 200px;
    }

    #catalogSort:focus {
        outline: none;
        border-color: var(--gold);
    }

    /* Categorías Pills mejoradas */
    .categories-section {
        margin-bottom: 2rem;
    }

    .categories-wrapper {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding: 1rem 0;
    }

    .cat-pill {
        background: white;
        border: 2px solid #e8e8e8;
        color: var(--dark);
        padding: 0.7rem 1.5rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cat-pill:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        border-color: var(--gold);
    }

    .cat-pill.active {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-secondary) 100%);
        color: var(--gold);
        border-color: var(--dark);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    /* Grid de productos responsive */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    /* Tarjetas de joyería premium */
    .jewel-card {
        background: var(--bg-card);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid #f0f0f0;
    }

    .jewel-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .jewel-media {
        position: relative;
        height: 320px;
        overflow: hidden;
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
    }

    .jewel-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .jewel-card:hover .jewel-media img {
        transform: scale(1.1);
    }

    .jewel-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
        color: var(--dark);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(212,175,55,0.4);
    }

    .jewel-body {
        padding: 1.8rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex-grow: 1;
    }

    .jewel-title {
        font-weight: 700;
        color: var(--dark);
        font-size: 1.2rem;
        margin: 0;
        text-align: center;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1.4;
    }

    .jewel-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-top: 1px solid #f0f0f0;
    }

    .jewel-code {
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .jewel-code strong {
        color: var(--dark);
        font-weight: 700;
    }

    .jewel-price {
        color: var(--gold);
        font-weight: 800;
        font-size: 1.4rem;
        letter-spacing: -0.5px;
    }

    .jewel-actions {
        margin-top: auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    .btn-view, .btn-whatsapp {
        padding: 0.9rem 1.2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-view {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-secondary) 100%);
        color: var(--gold);
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        color: white;
    }

    .btn-whatsapp {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: white;
    }

    .btn-whatsapp:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37,211,102,0.4);
    }

    /* Modal mejorado */
    .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-layout {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .modal-img {
        width: 100%;
        height: 500px;
        object-fit: contain;
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        padding: 1.5rem;
        border-radius: 16px;
    }

    .modal-info {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    #modalTitle {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--dark);
        margin: 0;
        line-height: 1.3;
    }

    #modalDesc {
        color: #666;
        font-size: 1rem;
        line-height: 1.6;
    }

    #modalPrice {
        font-weight: 800;
        color: var(--gold);
        font-size: 2rem;
        letter-spacing: -1px;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .modal-actions .btn {
        flex: 1;
        padding: 1rem 1.5rem;
        font-weight: 700;
        border-radius: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-dark {
        background: linear-gradient(135deg, var(--dark) 0%, var(--dark-secondary) 100%);
        border: none;
    }

    .btn-dark:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    .btn-success {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37,211,102,0.4);
    }

    /* Responsive design mejorado */
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 992px) {
        .catalog-title h1 {
            font-size: 2rem;
        }

        .modal-layout {
            grid-template-columns: 1fr;
        }

        .modal-img {
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        .catalog-container {
            padding: 1rem;
        }

        .catalog-header {
            padding: 1.5rem;
        }

        .catalog-title {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .catalog-title h1 {
            font-size: 1.6rem;
        }

        .catalog-title i {
            font-size: 2rem;
        }

        .search-controls {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        #catalogSort {
            width: 100%;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .jewel-media {
            height: 250px;
        }

        .jewel-body {
            padding: 1.2rem;
        }

        .jewel-title {
            font-size: 1rem;
            min-height: 50px;
        }

        .jewel-actions {
            grid-template-columns: 1fr;
        }

        .modal-img {
            height: 300px;
        }

        .modal-actions {
            flex-direction: column;
        }
    }

    @media (max-width: 576px) {
        .cat-pill {
            font-size: 0.85rem;
            padding: 0.6rem 1rem;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .jewel-media {
            height: 280px;
        }
    }
</style>

<div class="catalog-container">
    <!-- Header -->
    <div class="catalog-header">
        <div class="header-content">
            <div class="catalog-title">
                <i class="fas fa-gem"></i>
                <h1>{{ env('APP_NAME') }}</h1>
            </div>
            <p class="catalog-subtitle">Piezas únicas · Diseños exclusivos · Calidad garantizada</p>
        </div>
    </div>

    <!-- Controles de búsqueda -->
    <div class="search-controls">
        <div class="search-input-wrapper">
            <i class="fas fa-search"></i>
            <input id="catalogSearch" type="text" placeholder="Buscar por nombre, código o material...">
        </div>
        <select id="catalogSort">
            <option value="popular">Más populares</option>
            <option value="new">Nuevos primero</option>
            <option value="price_asc">Precio: menor a mayor</option>
            <option value="price_desc">Precio: mayor a menor</option>
        </select>
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
        @foreach($lstProducto as $producto)
            <div class="jewel-card" data-categoria="{{ $producto->id_categoria }}" data-material="{{ $producto->cNombreMaterial ?? '' }}">
                <div class="jewel-media">
                    @if($producto->img)
                        <img src="{{ asset('img/productos/'.$producto->img) }}" alt="{{ $producto->descripcion }}">
                    @else
                        <img src="{{ asset('img/logo.jpg') }}" alt="imagen">
                    @endif
                    @if($producto->es_nuevo ?? false)
                        <div class="jewel-badge">Nuevo</div>
                    @endif
                </div>
                <div class="jewel-body">
                    <div class="jewel-material">
                        <i class="fas fa-certificate"></i>
                        {{ $producto->cNombreMaterial ?? 'N/A' }}
                    </div>
                    <h3 class="jewel-title">{{ \Str::limit($producto->descripcion, 60) }}</h3>
                    <div class="jewel-meta">
                        <div class="jewel-code">Ref: <strong>{{ $producto->codigo_barras ?? '—' }}</strong></div>
                        <div class="jewel-price">${{ number_format($producto->precio_venta, 2) }}</div>
                    </div>
                    <div class="jewel-actions">
                        <button class="btn-view" 
                                data-img="@if($producto->img){{ asset('img/productos/'.$producto->img) }}@else{{ asset('img/logo.jpg') }}@endif" 
                                data-title="{{ $producto->descripcion }}"
                                data-material="{{ $producto->cNombreMaterial ?? 'N/A' }}"
                                data-categoria="{{ $producto->cNombreCategoria ?? 'N/A' }}"
                                data-price="{{ number_format($producto->precio_venta, 2) }}">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        <button class="btn-whatsapp" data-phone="" data-title="{{ $producto->descripcion }} - {{ $producto->cNombreMaterial ?? '' }}">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
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
                                <button type="button" class="btn btn-dark" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cerrar
                                </button>
                                <button id="modalWhatsapp" type="button" class="btn btn-success">
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
        // Búsqueda en tiempo real (incluyendo material)
        $('#catalogSearch').on('input', function(){
            var q = $(this).val().toLowerCase().trim();
            $('#productsGrid .jewel-card').each(function(){
                var title = $(this).find('.jewel-title').text().toLowerCase();
                var code = $(this).find('.jewel-code strong').text().toLowerCase();
                var material = $(this).data('material').toString().toLowerCase();
                $(this).toggle(title.indexOf(q) !== -1 || code.indexOf(q) !== -1 || material.indexOf(q) !== -1);
            });
        });

        // Filtro por categoría
        $('.cat-pill').on('click', function(){
            $('.cat-pill').removeClass('active');
            $(this).addClass('active');
            var cat = $(this).data('cat');
            $('#productsGrid .jewel-card').each(function(){
                if(cat === 'all') $(this).show();
                else $(this).toggle($(this).data('categoria') == cat);
            });
        });

        // Modal ver detalle
        $('.btn-view').on('click', function(){
            var img = $(this).data('img');
            var title = $(this).data('title');
            var material = $(this).data('material');
            var categoria = $(this).data('categoria');
            var price = $(this).data('price');
            
            $('#modalImage').attr('src', img);
            $('#modalTitle').text(title);
            $('#modalMaterial span').text(material);
            $('#modalCategoria span').text(categoria);
            $('#modalPrice').text('$' + price);
            $('#catalogImageModal').modal('show');
        });

        // WhatsApp rápido
        $('.btn-whatsapp, #modalWhatsapp').on('click', function(){
            var title = $(this).data('title') || $('#modalTitle').text();
            var material = $('#modalMaterial span').text() || '';
            // var phone = $(this).data('phone') || '';
            var phone = "9991629742"; // Número fijo para contacto';
            var text = 'Hola, estoy interesado en: ' + title + (material ? ' (' + material + ')' : '');
            var url = phone ? 'https://wa.me/' + phone.replace(/\D/g,'') + '?text=' + encodeURIComponent(text)
                            : 'https://web.whatsapp.com/send?text=' + encodeURIComponent(text);
            window.open(url, '_blank');
        });
    });
</script>

@endsection
