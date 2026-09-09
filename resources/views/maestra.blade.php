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
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1040;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            height: 56px;
        }

        .mobile-header a {
            font-size: 0.95rem;
            letter-spacing: 1px;
            color: #fff !important;
            transition: color 0.2s ease;
        }

        .mobile-header a:hover {
            color: #D4AF37 !important;
        }

        .mobile-header .btn-link {
            color: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.2s ease;
        }

        .mobile-header .btn-link:hover {
            color: #D4AF37 !important;
        }

        .mobile-header-icon {
            color: #dfb743;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .mobile-header .btn-link:hover .mobile-header-icon {
            color: #f1c40f;
            transform: scale(1.08);
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

        /* Notificaciones estilo Facebook dentro de Modal / Dropdown */
        .notification-list-container {
            max-height: 450px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s ease;
            text-decoration: none !important;
        }

        .notification-item:hover {
            background-color: #f8fafc;
        }

        .notification-icon-container {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: #eff6ff;
            color: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-right: 14px;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(59, 130, 246, 0.1);
        }

        /* Colores según el tipo de alerta (para escalabilidad futura) */
        .notification-item[data-type="apartado_inactivo"] .notification-icon-container {
            background-color: #ffe4e6;
            color: #e11d48;
            box-shadow: 0 2px 5px rgba(225, 29, 72, 0.1);
        }

        .notification-item[data-type="apartado_inactivo"] .notification-time {
            color: #e11d48;
        }

        .notification-item[data-type="stock_bajo"] .notification-icon-container {
            background-color: #fef3c7;
            color: #d97706;
            box-shadow: 0 2px 5px rgba(217, 119, 6, 0.1);
        }

        .notification-item[data-type="stock_bajo"] .notification-time {
            color: #d97706;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .notification-desc {
            font-size: 0.8rem;
            color: #475569;
            line-height: 1.4;
        }

        .notification-time {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 5px;
            display: block;
        }

        .badge-counter-sidebar {
            background-color: #ef4444;
            color: white;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 10px;
            margin-left: auto;
            border: 1.5px solid #1a1a1a;
            line-height: 1;
        }

        .badge-counter-mobile {
            position: absolute;
            top: -4px;
            right: -4px;
            font-size: 0.65rem;
            padding: 2px 5px;
            border-radius: 10px;
            font-weight: 800;
            border: 2px solid #0f172a;
            line-height: 1;
        }
    </style>
</head>
<body class="@auth authenticated @endauth">

@auth
    <!-- Cabecera Móvil (solo autenticados) -->
    <div class="mobile-header">
        <button class="btn btn-link text-white p-0 mr-3" id="botonMenuMobile" style="font-size: 1.5rem; text-decoration: none;">
            <i class="fas fa-bars mobile-header-icon"></i>
        </button>
        <a href="{{route("home")}}" class="text-white font-weight-bold text-uppercase" style="letter-spacing: 1.5px; font-size: 1.05rem; text-decoration: none; margin-right: auto;">
            {{env("APP_NAME")}}
        </a>
        <!-- Bell Mobile -->
        <button class="btn btn-link position-relative p-1 text-white mr-2" type="button" data-toggle="modal" data-target="#notificationsModal" style="font-size: 1.25rem; text-decoration: none;">
            <i class="far fa-bell mobile-header-icon"></i>
            <span class="badge badge-danger badge-counter-mobile d-none" id="notificationBadgeMobile">0</span>
        </button>
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
            <a class="sidebar-link" data-toggle="modal" data-target="#notificationsModal" style="cursor: pointer;">
                <i class="fas fa-bell" style="color: #D4AF37;"></i>
                <span>Notificaciones</span>
                <span class="badge badge-danger badge-counter-sidebar d-none" id="notificationBadge">0</span>
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
            @if (Auth::user()->hasPermission('manage_roles') || Auth::user()->hasPermission('manage_homepage') || Auth::user()->hasPermission('manage_general_settings'))
                <a class="sidebar-link {{ request()->routeIs(['roles.*', 'homepage.settings.*', 'general.settings.*', 'whatsapp.*']) ? '' : 'collapsed' }}" 
                   data-toggle="collapse" href="#configuracionesCollapse" role="button" 
                   aria-expanded="{{ request()->routeIs(['roles.*', 'homepage.settings.*', 'general.settings.*', 'whatsapp.*']) ? 'true' : 'false' }}" 
                   aria-controls="configuracionesCollapse">
                    <i class="fas fa-cog"></i>
                    <span>Configuraciones</span>
                    <i class="fas fa-chevron-down ml-auto submenu-arrow"></i>
                </a>
                <div class="collapse sidebar-submenu {{ request()->routeIs(['roles.*', 'homepage.settings.*', 'general.settings.*', 'whatsapp.*']) ? 'show' : '' }}" id="configuracionesCollapse">
 
                    @if(Auth::user()->hasPermission('manage_roles'))
                        <a class="sidebar-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{route("roles.index")}}">
                            <i class="fa fa-key" style="color: #D4AF37;"></i>Roles y Permisos
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage_general_settings'))
                        <a class="sidebar-link {{ request()->routeIs('general.settings.index') ? 'active' : '' }}" href="{{route("general.settings.index")}}">
                            <i class="fas fa-cogs" style="color: #D4AF37;"></i>
                            <span>Configuración General</span>
                        </a>
                    @endif

                    @if(Auth::user()->hasPermission('manage_homepage'))
                        <a class="sidebar-link {{ request()->routeIs('homepage.settings.index') ? 'active' : '' }}" href="{{route("homepage.settings.index")}}">
                            <i class="fas fa-sliders-h" style="color: #D4AF37;"></i>
                            <span>Configuración Portada</span>
                        </a>
                    @endif

                    <a class="sidebar-link {{ request()->routeIs('whatsapp.chat.index') ? 'active' : '' }}" href="{{route("whatsapp.chat.index")}}">
                        <i class="fas fa-comments" style="color: #25D366;"></i>
                        <span>Centro de Mensajes</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('whatsapp.links.index') ? 'active' : '' }}" href="{{route("whatsapp.links.index")}}">
                        <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                        <span>WhatsApp Enlaces / QR</span>
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('whatsapp.settings.index') ? 'active' : '' }}" href="{{route("whatsapp.settings.index")}}">
                        <i class="fas fa-robot" style="color: #25D366;"></i>
                        <span>WhatsApp Chatbot</span>
                    </a>
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
                <a class="sidebar-link {{ request()->routeIs('analytics.*') ? 'active' : '' }}" href="{{route("analytics.index")}}">
                    <i class="fab fa-google" style="color: #4285F4;"></i>
                    <span>Tráfico Web (GA4)</span>
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

    $(function(){
        // Función para cargar notificaciones del sistema
        function cargarNotificaciones() {
            $.ajax({
                url: "{{ route('notificaciones.api') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.lSuccess) {
                        var notifications = response.notifications || [];
                        var totalAlertas = notifications.length;

                        // Actualizar badges
                        if (totalAlertas > 0) {
                            $('#notificationBadge, #notificationBadgeMobile')
                                .text(totalAlertas)
                                .removeClass('d-none');
                        } else {
                            $('#notificationBadge, #notificationBadgeMobile').addClass('d-none');
                        }

                        // Rellenar lista de notificaciones (compartida en el modal)
                        var listHtml = '';
                        if (totalAlertas > 0) {
                            notifications.forEach(function(item) {
                                listHtml += `
                                    <a href="${item.url}" class="notification-item" data-type="${item.type}" data-id="${item.id}">
                                        <div class="notification-icon-container">
                                            <i class="${item.icon}"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">
                                                ${item.title}
                                            </div>
                                            <div class="notification-desc">
                                                ${item.description}
                                            </div>
                                            <span class="notification-time">
                                                <i class="far fa-clock mr-1"></i> ${item.time_ago}
                                            </span>
                                        </div>
                                    </a>
                                `;
                            });
                        } else {
                            listHtml = `
                                <div class="text-center p-5 text-muted">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0 small">No tienes notificaciones pendientes.</p>
                                </div>
                            `;
                        }

                        $('#notificationList').html(listHtml);
                    }
                },
                error: function() {
                    $('#notificationList').html(`
                        <div class="text-center p-4 text-danger small">
                            <i class="fas fa-exclamation-circle mr-1"></i> Error al cargar notificaciones
                        </div>
                    `);
                }
            });
        }

        // Interceptar click en notificaciones para marcarlas como leídas
        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            var href = $(this).attr('href');
            var type = $(this).data('type');
            var id = $(this).data('id');

            $.ajax({
                url: "{{ route('notificaciones.marcarLeida') }}",
                type: "POST",
                data: {
                    type: type,
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                complete: function() {
                    window.location.href = href;
                }
            });
        });

        // Cargar al iniciar y refrescar cada 5 minutos
        @auth
            cargarNotificaciones();
            setInterval(cargarNotificaciones, 5 * 60 * 1000);
        @endauth
    });
</script>

<div class="main-wrapper">
    <main class="container-fluid py-4">
        @yield("contenido")
    </main>
</div>

@auth
<!-- Modal de Notificaciones (Estilo Facebook) -->
<div class="modal fade" id="notificationsModal" tabindex="-1" role="dialog" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 50px rgba(0,0,0,0.2);">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-bottom: none; padding: 1.25rem 1.5rem;">
                <h5 class="modal-title font-weight-bold" id="notificationsModalLabel">
                    <i class="fas fa-bell mr-2" style="color: #D4AF37;"></i> Notificaciones
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="background-color: #f8fafc;">
                <div id="notificationList" class="notification-list-container">
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Cargando notificaciones...
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: 1px solid #e2e8f0; background: #fff; padding: 0.75rem;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 20px; padding: 5px 20px;">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endauth
</body>
</html>
