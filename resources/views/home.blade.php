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
@extends('maestra')
@section('titulo', 'Inicio')

@section('contenido')

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<style>
    .home-container {
        padding: 2rem 0;
    }

    .welcome-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .welcome-header h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        letter-spacing: 0.5px;
    }

    .welcome-header .user-name {
        color: #D4AF37;
        font-weight: 800;
    }

    .welcome-header .subtitle {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
        color: #e0e0e0;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .module-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .module-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .module-icon-container {
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .module-icon-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .module-card:hover .module-icon-container::before {
        opacity: 1;
    }

    .module-card:hover .module-icon-container i {
        transform: scale(1.1);
    }

    .module-icon-container i {
        transition: transform 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .module-body {
        padding: 1.5rem;
        background: white;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .module-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 1rem 0;
        text-align: center;
        letter-spacing: 0.3px;
    }

    .btn-module-access {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #D4AF37;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .btn-module-access:hover {
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }

    .btn-module-access:active {
        transform: scale(0.98);
    }

    @media (max-width: 768px) {
        .modules-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
        }

        .welcome-header h1 {
            font-size: 1.8rem;
        }

        .module-icon-container {
            height: 150px;
            font-size: 3.5rem;
        }
    }
</style>

<div class="container home-container">
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h1>
            <i class="fas fa-home" style="margin-right: 0.8rem; color: #D4AF37;"></i>
            ¡Bienvenido <span class="user-name">{{ Auth::user()->name }}</span>!
        </h1>
        <p class="subtitle">
            <i class="far fa-calendar-alt" style="margin-right: 0.5rem;"></i>
            {{ date('d/m/Y') }} · {{ env("APP_NAME") }}
        </p>
    </div>

    @php
        $modulos = Auth::user()->id == 1
            ? [
                ["nombre" => "productos", "color" => "#FF6B6B", "icono" => "fa-box"],
                ["nombre" => "estadisticas", "color" => "#4ECDC4", "icono" => "fa-chart-bar"],
                ["nombre" => "ventas", "color" => "#45B7D1", "icono" => "fa-list"],
                ["nombre" => "vender", "color" => "#FFA502", "icono" => "fa-shopping-cart"],
                ["nombre" => "clientes", "color" => "#95E1D3", "icono" => "fa-users"],
                ["nombre" => "usuarios", "color" => "#C44569", "icono" => "fa-user-tie"],
            ]
            : [
                ["nombre" => "productos", "color" => "#FF6B6B", "icono" => "fa-box"],
                ["nombre" => "ventas", "color" => "#45B7D1", "icono" => "fa-list"],
                ["nombre" => "vender", "color" => "#FFA502", "icono" => "fa-shopping-cart"],
            ];
    @endphp

    <!-- Modules Grid -->
    <div class="modules-grid">
        @foreach($modulos as $modulo)
            <div class="module-card">
                <div class="module-icon-container" style="background: {{ $modulo['color'] }};">
                    <i class="fa {{ $modulo['icono'] }}"></i>
                </div>
                <div class="module-body">
                    <h5 class="module-title">
                        @switch($modulo['nombre'])
                            @case("productos")
                                Productos
                                @break
                            @case("estadisticas")
                                Estadísticas
                                @break
                            @case("ventas")
                                Ventas
                                @break
                            @case("vender")
                                Vender
                                @break
                            @case("clientes")
                                Clientes
                                @break
                            @case("usuarios")
                                Usuarios
                                @break
                            @default
                                {{ str_replace('_', ' ', ucfirst($modulo['nombre'])) }}
                        @endswitch
                    </h5>
                    <a href="{{ route($modulo['nombre'] . ".index") }}" class="btn btn-module-access">
                        <i class="fas fa-arrow-right"></i>
                        Acceder
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

