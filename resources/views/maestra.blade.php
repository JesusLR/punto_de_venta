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
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.ico') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{env("APP_NAME")}}">
    <meta name="author" content="Parzibyte">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("titulo") - {{env("APP_NAME")}}</title>
    @auth
    <script>
        window.User = {
            id: {{ Auth::user()->id }},
            name: "{{ Auth::user()->name }}",
            permissions: @json(Auth::user()->getAllPermissions())
        };
        function hasPermission(permission) {
            return window.User && window.User.permissions && window.User.permissions.includes(permission);
        }
    </script>
    @endauth
    <link href="{{url("/css/bootstrap.min.css")}}" rel="stylesheet">
    <link href="{{url("/css/all.min.css")}}" rel="stylesheet">
    <link rel="stylesheet" href="{{url('//cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.13.1/bootstrap-table.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8fafc;
            padding-top: 0;
            padding-bottom: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
        }

        /* Barra Lateral */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 260px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            border-right: 2px solid #D4AF37;
            z-index: 1050;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand {
            padding: 1.5rem;
            font-size: 1.15rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #fff !important;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand i {
            color: #D4AF37;
            font-size: 1.25rem;
        }

        /* Navegación */
        .sidebar-nav {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .nav-section-title {
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.75rem 1.5rem 0.25rem 1.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none !important;
            border-left: 4px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 0.75rem;
            cursor: pointer;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #D4AF37 !important;
            background: rgba(255, 255, 255, 0.04);
            border-left-color: #D4AF37;
            padding-left: 1.75rem;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .sidebar-link:hover i {
            transform: scale(1.1);
        }

        /* Submenús */
        .sidebar-submenu {
            background: rgba(0, 0, 0, 0.15);
            padding-left: 0.75rem;
        }

        .sidebar-submenu .sidebar-link {
            padding: 0.6rem 1.5rem 0.6rem 1.8rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .submenu-arrow {
            font-size: 0.75rem !important;
            transition: transform 0.2s ease-in-out;
        }

        .sidebar-link[aria-expanded="true"] .submenu-arrow {
            transform: rotate(180deg);
        }

        /* Cabecera Móvil */
        .mobile-header {
            display: none;
            background: #0f172a;
            color: white;
            padding: 0.8rem 1.5rem;
            border-bottom: 2px solid #D4AF37;
            z-index: 1040;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1030;
            transition: all 0.3s ease;
        }

        /* Layout del Contenido */
        .main-wrapper {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Responsividad */
        @media (min-width: 992px) {
            body.authenticated .main-wrapper {
                margin-left: 260px;
            }
            body.authenticated .mobile-header {
                display: none;
            }
            body:not(.authenticated) .sidebar {
                display: none;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                left: -260px;
            }
            .sidebar.active {
                left: 0;
            }
            body.authenticated .mobile-header {
                display: flex;
            }
            body.authenticated .main-wrapper {
                padding-top: 60px; /* Offset para mobile header */
            }
            .sidebar-overlay.active {
                display: block;
            }
            body:not(.authenticated) .sidebar {
                display: none;
            }
            body:not(.authenticated) .mobile-header {
                display: none;
            }
        }
    </style>
</head>
<body class="@auth authenticated @endauth">

@auth
    <!-- Cabecera Móvil (solo autenticados) -->
    <div class="mobile-header">
        <button class="btn btn-link text-white p-0 mr-3" id="botonMenuMobile" style="font-size: 1.5rem; text-decoration: none;">
            <i class="fas fa-bars" style="color: #D4AF37;"></i>
        </button>
        <a href="{{route("home")}}" class="text-white font-weight-bold text-uppercase" style="letter-spacing: 1.5px; font-size: 1.05rem; text-decoration: none;">
            {{env("APP_NAME")}}
        </a>
        <div style="width: 24px;"></div> <!-- Espaciador para centrar -->
    </div>

    <!-- Sidebar (solo autenticados) -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <a class="text-white text-decoration-none font-weight-bold d-flex align-items-center" href="{{route("home")}}" style="letter-spacing: 1px;">
                <i class="fa fa-shopping-cart mr-2" style="color: #D4AF37; font-size: 1.25rem;"></i> <span class="text-white" style="margin-left: 12px;"> {{env("APP_NAME")}}
            </a>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-title">General</div>
            <a class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{route("home")}}">
                <i class="fa fa-home"></i>
                <span>Inicio</span>
            </a>
            <a class="sidebar-link" href="{{ route('inicio') }}" target="_blank">
                <i class="fas fa-external-link-alt" style="color: #64748b;"></i>
                <span>Ver Portada</span>
            </a>
            <a class="sidebar-link" href="{{ route('catalogoProductos.index') }}" target="_blank">
                <i class="fas fa-gem" style="color: #D4AF37;"></i>
                <span>Ver Catálogo</span>
            </a>

            <div class="nav-section-title">Administración</div>
            
            {{-- Catálogos --}}
            @if (Auth::user()->hasPermission('ver_categorias') || Auth::user()->hasPermission('ver_clientes') || Auth::user()->hasPermission('ver_materiales') || Auth::user()->hasPermission('ver_proveedores') || Auth::user()->hasPermission('ver_usuarios'))
                <a class="sidebar-link {{ request()->routeIs(['categorias.*', 'clientes.*', 'materiales.*', 'proveedores.*', 'usuarios.*']) ? '' : 'collapsed' }}" 
                   data-toggle="collapse" href="#catalogsCollapse" role="button" 
                   aria-expanded="{{ request()->routeIs(['categorias.*', 'clientes.*', 'materiales.*', 'proveedores.*', 'usuarios.*']) ? 'true' : 'false' }}" 
                   aria-controls="catalogsCollapse">
                    <i class="fas fa-book"></i>
                    <span>Catálogos</span>
                    <i class="fas fa-chevron-down ml-auto submenu-arrow"></i>
                </a>
                <div class="collapse sidebar-submenu {{ request()->routeIs(['categorias.*', 'clientes.*', 'materiales.*', 'proveedores.*', 'usuarios.*']) ? 'show' : '' }}" id="catalogsCollapse">
                    @if(Auth::user()->hasPermission('ver_categorias'))
                        <a class="sidebar-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{route("categorias.index")}}">
                            <i class="fa fa-tags" style="color: #FF6B6B;"></i>Categorías
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('ver_clientes'))
                        <a class="sidebar-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{route("clientes.index")}}">
                            <i class="fa fa-users" style="color: #4ECDC4;"></i>Clientes
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('ver_materiales'))
                        <a class="sidebar-link {{ request()->routeIs('materiales.*') ? 'active' : '' }}" href="{{route("materiales.index")}}">
                            <i class="fa fa-hammer" style="color: #45B7D1;"></i>Materiales
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('ver_proveedores'))
                        <a class="sidebar-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}" href="{{route("proveedores.index")}}">
                            <i class="fa fa-truck" style="color: #FFA502;"></i>Proveedores
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('ver_usuarios'))
                        <a class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{route("usuarios.index")}}">
                            <i class="fa fa-user-tie" style="color: #95E1D3;"></i>Usuarios
                        </a>
                    @endif
                </div>
            @endif

            {{-- Configuracion --}}
            @if (Auth::user()->hasPermission('manage_roles') || Auth::user()->hasPermission('manage_homepage'))
                <a class="sidebar-link {{ request()->routeIs(['roles.*', 'homepage.settings.*']) ? '' : 'collapsed' }}" 
                   data-toggle="collapse" href="#configuracionesCollapse" role="button" 
                   aria-expanded="{{ request()->routeIs(['roles.*', 'homepage.settings.*']) ? 'true' : 'false' }}" 
                   aria-controls="configuracionesCollapse">
                    <i class="fas fa-cog"></i>
                    <span>Configuraciones</span>
                    <i class="fas fa-chevron-down ml-auto submenu-arrow"></i>
                </a>
                <div class="collapse sidebar-submenu {{ request()->routeIs(['roles.*', 'homepage.settings.*']) ? 'show' : '' }}" id="configuracionesCollapse">
 
                    @if(Auth::user()->hasPermission('manage_roles'))
                        <a class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{route("roles.index")}}">
                            <i class="fa fa-key" style="color: #D4AF37;"></i>Roles y Permisos
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage_homepage'))
                        <a class="sidebar-link {{ request()->routeIs('homepage.settings.index') ? 'active' : '' }}" href="{{route("homepage.settings.index")}}">
                            <i class="fas fa-sliders-h" style="color: #D4AF37;"></i>
                            <span>Configuración Portada</span>
                        </a>
                    @endif
                </div>
            @endif

            @if(Auth::user()->hasPermission('ver_productos'))
                <a class="sidebar-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{route("productos.index")}}">
                    <i class="fa fa-box"></i>
                    <span>Productos</span>
                </a>
            @endif

            @if(Auth::user()->hasPermission('view_statistics'))
                <a class="sidebar-link {{ request()->routeIs('estadisticas.*') ? 'active' : '' }}" href="{{route("estadisticas.index")}}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Estadísticas</span>
                </a>
            @endif

            {{-- Tienda --}}
            @if (Auth::user()->hasPermission('make_sales') || Auth::user()->hasPermission('ver_apartados') || Auth::user()->hasPermission('view_sales') || Auth::user()->hasPermission('manage_finances'))
                <a class="sidebar-link {{ request()->routeIs(['vender.*', 'apartados.*', 'ventas.*', 'finanzas.*']) ? '' : 'collapsed' }}" 
                   data-toggle="collapse" href="#ventasCollapse" role="button" 
                   aria-expanded="{{ request()->routeIs(['vender.*', 'apartados.*', 'ventas.*', 'finanzas.*']) ? 'true' : 'false' }}" 
                   aria-controls="ventasCollapse">
                    <i class="fas fa-store"></i>
                    <span>Tienda</span>
                    <i class="fas fa-chevron-down ml-auto submenu-arrow"></i>
                </a>
                <div class="collapse sidebar-submenu {{ request()->routeIs(['vender.*', 'apartados.*', 'ventas.*', 'finanzas.*']) ? 'show' : '' }}" id="ventasCollapse">
                    @if(Auth::user()->hasPermission('make_sales'))
                        <a class="sidebar-link {{ request()->routeIs('vender.*') ? 'active' : '' }}" href="{{route("vender.index")}}">
                            <i class="fa fa-cart-plus" style="color: #95E1D3;"></i>Vender
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('ver_apartados'))
                        <a class="sidebar-link {{ request()->routeIs('apartados.*') ? 'active' : '' }}" href="{{route("apartados.index")}}">
                            <i class="fas fa-clipboard-list" style="color: #FF6B6B;"></i>Apartados
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('view_sales'))
                        <a class="sidebar-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" href="{{route("ventas.index")}}">
                            <i class="fa fa-list" style="color: #45B7D1;"></i>Ventas
                        </a>
                    @endif
                    @if(Auth::user()->hasPermission('manage_finances'))
                        <a class="sidebar-link {{ request()->routeIs('finanzas.*') ? 'active' : '' }}" href="{{route("finanzas.index")}}">
                            <i class="fas fa-wallet" style="color: #2ECC71;"></i>Finanzas
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="sidebar-footer" style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.08);">
            <a class="sidebar-link p-2" href="{{route("logout")}}" style="border-radius: 8px;">
                <i class="fa fa-sign-out-alt"></i>
                <span>Salir ({{ Auth::user()->name }})</span>
            </a>
        </div>
    </div>

    <!-- Overlay para Móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
@endauth

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.querySelector(".sidebar");
        const overlay = document.querySelector(".sidebar-overlay");
        const btnMenu = document.querySelector("#botonMenuMobile");
        
        if (btnMenu) {
            btnMenu.addEventListener("click", () => {
                sidebar.classList.toggle("active");
                overlay.classList.toggle("active");
            });
        }
        
        if (overlay) {
            overlay.addEventListener("click", () => {
                sidebar.classList.remove("active");
                overlay.classList.remove("active");
            });
        }
    });
</script>

<div class="main-wrapper">
    <main class="container-fluid py-4">
        @yield("contenido")
    </main>
</div>
</body>
</html>
