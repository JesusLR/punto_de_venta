{{--

  ____          _____               _ _           _
 |  _ \        |  __ \             (_) |         | |
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |
        |___/                               |___/

    Blog:       https://parzibyte.me/blog
    Ayuda:      https://parzibyte.me/blog/contrataciones-ayuda/
    Contacto:   https://parzibyte.me/blog/contacto/

    Copyright (c) 2020 Luis Cabrera Benito
    Licenciado bajo la licencia MIT

    El texto de arriba debe ser incluido en cualquier redistribucion
--}}
<!doctype html>
<html lang="es">
<!--

  ____          _____               _ _           _
 |  _ \        |  __ \             (_) |         | |
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |
        |___/                               |___/

    Blog:       https://parzibyte.me/blog
    Ayuda:      https://parzibyte.me/blog/contrataciones-ayuda/
    Contacto:   https://parzibyte.me/blog/contacto/

    Copyright (c) 2020 Luis Cabrera Benito
    Licenciado bajo la licencia MIT

    El texto de arriba debe ser incluido en cualquier redistribucion
-->

<head>
    @if(config('services.google.analytics_id'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '{{ config('services.google.analytics_id') }}');
        </script>
    @endif
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=colibri">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}?v=colibri">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{env("APP_NAME")}}">
    <meta name="author" content="Parzibyte">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("titulo") - {{env("APP_NAME")}}</title>
    <link href="{{url("/css/bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{url("/css/all.min.css")}}" rel="stylesheet">
    <link rel="stylesheet" href="{{url('//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.13.1/bootstrap-table.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/boutique.css') }}?v={{ filemtime(public_path('css/boutique.css')) }}">
</head>
<body class="boutique-site">

<nav class="navbar navbar-expand-md fixed-top">
    {{-- <a class="navbar-brand" target="" href="{{route("home")}}"> --}}
    <a class="navbar-brand" href="{{ route('inicio') }}">
        <span class="brand-emblem" aria-hidden="true"><i class="far fa-gem"></i></span>
        <span>{{ config('app.name') }}<small>JOYERÍA & COLECCIONES</small></span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            id="botonMenu" aria-label="Mostrar u ocultar menú" aria-controls="menu" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('inicio') ? 'active' : '' }}" href="{{ route('inicio') }}">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('catalogoProductos.index') ? 'active' : '' }}" href="{{ route('catalogoProductos.index') }}">Colección</a>
            </li>
            @guest
            @else
            <!-- @if (Auth::user()->id == 1)
                <li class="nav-item">
                    <a class="nav-link" href="{{route("home")}}"><i class="fa fa-home mr-1"></i>Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-book mr-1"></i>Catálogos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="categoriasDropdown">
                        <a class="dropdown-item" href="{{route("categorias.index")}}"><i class="fa fa-tags mr-2" style="color: #FF6B6B;"></i>Categorías</a>
                        <a class="dropdown-item" href="{{route("clientes.index")}}"><i class="fa fa-users mr-2" style="color: #4ECDC4;"></i>Clientes</a>
                        <a class="dropdown-item" href="{{route("materiales.index")}}"><i class="fa fa-hammer mr-2" style="color: #45B7D1;"></i>Materiales</a>
                        <a class="dropdown-item" href="{{route("proveedores.index")}}"><i class="fa fa-truck mr-2" style="color: #FFA502;"></i>Proveedores</a>
                        <a class="dropdown-item" href="{{route("usuarios.index")}}"><i class="fa fa-user-tie mr-2" style="color: #95E1D3;"></i>Usuarios</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("productos.index")}}"><i class="fa fa-box mr-1"></i>Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("estadisticas.index")}}"><i class="fas fa-chart-pie mr-1"></i>Estadísticas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("vender.index")}}"><i class="fa fa-cart-plus mr-1"></i>Vender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("ventas.index")}}"><i class="fa fa-list mr-1"></i>Ventas</a>
                </li>
            @else
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{route("home")}}"><i class="fa fa-home mr-1"></i>Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("productos.index")}}"><i class="fa fa-box mr-1"></i>Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("vender.index")}}"><i class="fa fa-cart-plus mr-1"></i>Vender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("ventas.index")}}"><i class="fa fa-list mr-1"></i>Ventas</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{route("home")}}"><i class="fa fa-home mr-1"></i>Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-book mr-1"></i>Catálogos
                    </a>
                    <div class="dropdown-menu" aria-labelledby="categoriasDropdown">
                        <a class="dropdown-item" href="{{route("categorias.index")}}"><i class="fa fa-tags mr-2" style="color: #FF6B6B;"></i>Categorías</a>
                        <a class="dropdown-item" href="{{route("clientes.index")}}"><i class="fa fa-users mr-2" style="color: #4ECDC4;"></i>Clientes</a>
                        <a class="dropdown-item" href="{{route("materiales.index")}}"><i class="fa fa-hammer mr-2" style="color: #45B7D1;"></i>Materiales</a>
                        <a class="dropdown-item" href="{{route("proveedores.index")}}"><i class="fa fa-truck mr-2" style="color: #FFA502;"></i>Proveedores</a>
                        <a class="dropdown-item" href="{{route("usuarios.index")}}"><i class="fa fa-user-tie mr-2" style="color: #95E1D3;"></i>Usuarios</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("productos.index")}}"><i class="fa fa-box mr-1"></i>Productos</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{route("estadisticas.index")}}"><i class="fas fa-chart-pie mr-1"></i>Estadísticas</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{route("vender.index")}}"><i class="fa fa-cart-plus mr-1"></i>Vender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route("ventas.index")}}"><i class="fa fa-list mr-1"></i>Ventas</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="ventasDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-book mr-1"></i>Tienda
                    </a>
                    <div class="dropdown-menu" aria-labelledby="ventasDropdown">
                        <a class="dropdown-item" href="{{route("vender.index")}}"><i class="fa fa-cart-plus mr-2" style="color: #95E1D3;"></i>Vender</a>
                        <a class="dropdown-item" href="{{route("apartados.index")}}"><i class="fas fa-clipboard-list mr-2" style="color: #FF6B6B;"></i>Apartados</a>
                        <a class="dropdown-item" href="{{route("ventas.index")}}"><i class="fa fa-list mr-2" style="color: #45B7D1;"></i>Ventas</a>
                        {{-- <a class="dropdown-item" href="{{route("finanzas.index")}}"><i class="fas fa-wallet mr-2" style="color: #2ECC71;"></i>Finanzas</a> --}}
                    </div>
                </li>
            @endif -->

            @endguest
        </ul>
        <ul class="navbar-nav ml-auto">
            @guest
                <!-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}"><i class="fa fa-sign-in-alt mr-1"></i>Iniciar Sesión</a>
                </li> -->
            @else
                @auth
                    <li class="nav-item">
                        <a href="{{route("logout")}}" class="nav-link">
                            <i class="fa fa-sign-out-alt mr-1"></i>Salir ({{ Auth::user()->name }})
                        </a>
                    </li>
                @endauth
            @endguest
        </ul>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    // Tomado de https://parzibyte.me/blog/2019/06/26/menu-responsivo-bootstrap-4-sin-dependencias/
    document.addEventListener("DOMContentLoaded", () => {
        const menu = document.querySelector("#menu"),
            botonMenu = document.querySelector("#botonMenu");
        if (menu) {
            botonMenu.addEventListener("click", () => {
                const expanded = menu.classList.toggle("show");
                botonMenu.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            });
        }
    });
</script>
<main class="container-fluid">
    @yield("contenido")
</main>

{{-- <footer class="px-2 py-2 fixed-bottom bg-dark">
    <span class="text-muted">Punto de venta en Laravel
        <i class="fa fa-code text-white"></i>
        con
        <i class="fa fa-heart" style="color: #ff2b56;"></i>
        por
        <a class="text-white" href="//parzibyte.me/blog">Parzibyte</a>
        &nbsp;|&nbsp;
        <a target="_blank" class="text-white" href="//github.com/parzibyte/sistema_ventas_laravel">
            <i class="fab fa-github"></i>
        </a>
    </span>
</footer> --}}
</body>
</html>
