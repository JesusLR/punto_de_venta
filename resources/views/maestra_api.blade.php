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
    <style>
        body {
            padding-top: 70px;
            padding-bottom: 0;
        }

        /* Navbar Mejorado */
        .navbar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d3436 50%, #1a1a1a 100%) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border-bottom: 2px solid #FF6B6B;
            padding: 1rem 2rem;
        }

        .navbar-brand {
            font-weight: 900;
            font-size: 1.2rem;
            letter-spacing: 1.5px;
            color: #fff !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
        }

        .navbar-brand:hover {
            text-shadow: 0 0 20px rgba(255, 107, 107, 0.6);
            transform: scale(1.08) translateX(5px);
        }

        .navbar-brand i {
            transition: all 0.4s ease-in-out;
            font-size: 1.1rem;
        }

        .navbar-brand:hover i {
            animation: spin 0.6s ease-in-out;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            margin: 0 8px;
            border-radius: 8px;
            position: relative;
            color: #fff !important;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 0.5rem 0.75rem !important;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #FF6B6B, #FFA502);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-link:hover::before {
            width: 100%;
            box-shadow: 0 0 15px rgba(255, 107, 107, 0.5);
        }

        .nav-link:hover {
            color: #FFD700 !important;
            transform: translateY(-3px);
            background: rgba(255, 107, 107, 0.1);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .nav-link i {
            transition: all 0.3s ease-in-out;
            font-size: 0.9rem;
        }

        .nav-link:hover i {
            transform: scale(1.15);
            filter: drop-shadow(0 0 5px rgba(255, 215, 0, 0.5));
        }

        .dropdown-menu {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 100%);
            border: 1px solid rgba(255, 107, 107, 0.3);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.4);
            border-radius: 12px;
            animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            color: #fff !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.65rem 1.5rem;
            border-left: 3px solid transparent;
            font-weight: 500;
            letter-spacing: 0.3px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: rgba(255, 107, 107, 0.15);
            border-left-color: #FF6B6B;
            padding-left: 2rem;
            transform: translateX(5px);
            box-shadow: inset 0 0 10px rgba(255, 107, 107, 0.1);
        }

        .dropdown-item i {
            transition: all 0.3s ease-in-out;
            font-size: 0.85rem;
        }

        .dropdown-item:hover i {
            transform: scale(1.15);
        }

        .dropdown-divider {
            border-color: rgba(255, 107, 107, 0.2);
            margin: 0.5rem 0;
        }

        .navbar-toggler {
            border: 2px solid #FF6B6B;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease-in-out;
        }

        .navbar-toggler:hover {
            box-shadow: 0 0 15px rgba(255, 107, 107, 0.5);
            transform: scale(1.1);
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23FF6B6B' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md fixed-top">
    {{-- <a class="navbar-brand" target="" href="{{route("home")}}"> --}}
    <a class="navbar-brand" target="" href="#">
        <i class="fa fa-shopping-cart mr-2"></i>{{env("APP_NAME")}}
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            id="botonMenu" aria-label="Mostrar u ocultar menú">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav mr-auto">
            @guest
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                </li> --}}

                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">
                        Registro
                    </a>
                </li> --}}
            @else
            @if (Auth::user()->id == 1)
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
                <li class="nav-item">
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
                </li>
            @endif

            @endguest
        </ul>
        {{-- <ul class="navbar-nav ml-auto">
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}"><i class="fa fa-sign-in-alt mr-1"></i>Login</a>
                </li>
            @else
                @auth
                    <li class="nav-item">
                        <a href="{{route("logout")}}" class="nav-link">
                            <i class="fa fa-sign-out-alt mr-1"></i>Salir ({{ Auth::user()->name }})
                        </a>
                    </li>
                @endauth
            @endguest
        </ul> --}}
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
            botonMenu.addEventListener("click", () => menu.classList.toggle("show"));
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
