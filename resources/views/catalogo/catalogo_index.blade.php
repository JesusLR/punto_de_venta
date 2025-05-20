<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <title>{{env("APP_NAME")}}</title>

    {{-- <link href="{{url("/css/bootstrap.min.css")}}" rel="stylesheet"> --}}
    {{-- <link href="{{url("/css/all.min.css")}}" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="{{url('//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.13.1/bootstrap-table.min.css')}}"> --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.css"> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

    <link rel="stylesheet" href="{{url('//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.13.1/bootstrap-table.min.css')}}">
    <!-- Vinculamos Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los iconos -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <!-- Estilos adicionales -->
    <style>

        @media (max-width: 768px) {
        #btnRegresarCatalogo {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        }

        body {
            font-family: 'Helvetica Neue', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            position: relative;
            z-index: 0;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('img/logo.jpg') }}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.05; /* Puedes subir o bajar este valor */
            z-index: -1;
            pointer-events: none;
        }

        /* NAVBAR transparente */
        .navbar {
            background-color: transparent !important;
            box-shadow: none;
        }
        .navbar-brand {
            /* font-weight: bold;
            font-size: 24px;
            color: #c08497; */
            font-family: 'Dancing Script', cursive;
            font-size: 32px;
            font-weight: 700;
            color: #c08497;
        }

        .navbar-nav .nav-link {
            color: #c08497;
            font-weight: 600;
        }

        .navbar-nav .nav-link:hover {
            color: #ad5d85;
        }

        .categoria-titulo {
            margin-top: 40px;
            margin-bottom: 20px;
            color: #c08497;
            /* font-size: 2.5rem;
            font-weight: 600; */
            text-transform: uppercase;

            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: #c08497;
        }

        .producto-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .producto-img {
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
        }

        .producto-img:hover {
            transform: scale(1.05);
        }

        .card-body {
            background-color: #fff5f7;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .card-title {
            /* font-size: 1.25rem; */
            /* font-weight: 600; */
            /* color: #ad5d85; */
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: #ad5d85;
        }

        .card-text {
            font-size: 1.1rem;
            color: #666;
        }

        .btn-primary {
            background-color: #c08497;
            border-color: #c08497;
            border-radius: 20px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #ad5d85;
            border-color: #ad5d85;
        }

        .card-body .btn-primary {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }

        /* Estilo para el footer */
        footer {
            /* background-color: #c08497;
            color: white;
            padding: 30px 0;
            text-align: center; */

            background-color: transparent;
            color: #c08497;
            padding: 30px 0;
            text-align: center;
        }

        footer .footer-text {
            font-size: 1.1rem;
        }

        footer .footer-text a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        footer .footer-text a:hover {
            color: #d5a7c0;
        }

        footer .social-icons i {
            font-size: 24px;
            margin: 0 10px;
            color: #fff;
        }

        footer .social-icons i:hover {
            color: #d5a7c0;
        }

        .footer-text strong {
            font-family: 'Dancing Script', cursive;
            font-size: 28px;
            font-weight: 700;
        }

        /* Estilo para las tarjetas de productos */
        .row {
            margin-top: 20px;
        }

        .producto-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .producto-img {
            height: 200px; /* Ajusta a tu gusto */
            object-fit: cover; /* Recorta la imagen sin deformarla */
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-body .btn {
            margin-top: auto;
        }

    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">{{env("APP_NAME")}}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownProductos" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Productos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownProductos">
                        @foreach ($lstCategorias as $categoria)
                            <h6 class="dropdown-header" no-click>{{$categoria->cNombreCategoria}}</h6>
                                @foreach ($lstMateriales as $material)
                                    @foreach ($lstProducto as $producto)
                                        @if($producto->id_categoria == $categoria->id && $producto->id_material == $material->id)
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            onclick="verCategoria({{ $categoria->id }}, {{ $material->id }}, '{{ $categoria->cNombreCategoria . ' ' . $material->cNombreMaterial }}')">
                                            {{ $categoria->cNombreCategoria . ' ' . $material->cNombreMaterial }}
                                        </a>
                                        @endif
                                    @endforeach
                                @endforeach    
                        @endforeach

                        
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Contenedor de productos -->
    <div class="container"  id="containerUno">
        
        @foreach ($lstCategorias as $categoria)
            <div class="categoria-titulo">
                <h2>{{$categoria->cNombreCategoria}}</h2>
            </div>
            <div class="row">
                @foreach ($lstMateriales as $material)
                    @foreach ($lstProducto as $producto)
                        @if($producto->id_categoria == $categoria->id && $producto->id_material == $material->id)
                            <div class="col-md-4 d-flex mb-4">
                                <div class="card producto-card flex-fill">
                                    <img @if ($producto->img == null) src="{{ asset('img/logo.jpg') }}" @else src="{{ asset('img/productos/' . $producto->img) }}" @endif  class="card-img-top producto-img" alt="{{ $categoria->cNombreCategoria . ' ' . $material->cNombreMaterial }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $categoria->cNombreCategoria . ' ' . $material->cNombreMaterial }}</h5>
                                        {{-- <a href="#" class="btn btn-primary">Ver más</a> --}}
                                        <button type="button" class="btn btn-primary" onclick="verCategoria({{$categoria->id}}, {{$material->id}}, '{{ $categoria->cNombreCategoria . ' ' . $material->cNombreMaterial }}');">Ver más</button>
                                    </div>
                                </div>
                            </div>  
                        @endif
                    @endforeach
                @endforeach
                
            </div>
        @endforeach
    </div>

    <div class="container" id="containerDos" style="display:none;">
        <div class="categoria-titulo">
            {{-- <button type="button" class="btn btn-secondary mb-3" onclick="window.history.back();">
                ← Regresar
            </button> --}}
            <button type="button" class="btn btn-primary" id="btnRegresarCatalogo">← Regresar</button>
            <h2 id="divTituloContainerDos"></h2>
            <div id="divProdutos" class="container">

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-text">
                <p><strong>{{env("APP_NAME")}}</strong></p>
                {{-- <p><i class="fas fa-map-marker-alt"></i> Dirección: Calle Ficticia 123, Ciudad, País</p> --}}
                {{-- <p><i class="fas fa-phone-alt"></i> Teléfono: +1 234 567 890</p> --}}
                {{-- <p>© 2025 {{env("APP_NAME")}}. Todos los derechos reservados.</p> --}}
            </div>
        </div>
    </footer>

    @include('catalogo.modals.imagen')

    <!-- Scripts de Bootstrap -->
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> --}}
    
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script>
         const baseAssetUrl = "{{ asset('img/productos/') }}/";
         const defaultImg = "{{ asset('img/logo.jpg') }}";
    </script>

    <script src="{{asset('js/catalogo.js')}}"></script>

</body>
</html>
