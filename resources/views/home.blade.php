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

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #D4AF37;
    }

    .kpi-card small {
        display: block;
        color: #666;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
        margin-bottom: 0.35rem;
    }

    .kpi-card strong {
        font-size: 1.4rem;
        color: #1a1a1a;
        font-weight: 800;
    }

    .kpi-card .kpi-sub {
        font-size: 0.8rem;
        color: #888;
        margin-top: 0.35rem;
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

        .kpi-grid {
            grid-template-columns: repeat(2, minmax(180px, 1fr));
        }

        .welcome-header h1 {
            font-size: 1.8rem;
        }

        .module-icon-container {
            height: 150px;
            font-size: 3.5rem;
        }
    }

    @media (max-width: 576px) {
        .kpi-grid {
            grid-template-columns: 1fr;
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

    <div class="kpi-grid">
        @if(Auth::user()->id == 1)
            <div class="kpi-card">
                <small>Ingresos automáticos</small>
                <strong style="color: #2E7D32">${{ number_format((float) $ingresosAutomaticosMes, 2) }}</strong>
                <div class="kpi-sub">{{ $inicioMes->format('d/m/Y') }} al {{ $finMes->format('d/m/Y') }}</div>
            </div>
            <div class="kpi-card">
                <small>Egresos capturados</small>
                <strong style="color: #C62828">${{ number_format((float) $egresosCapturadosMes, 2) }}</strong>
                <div class="kpi-sub">Mes en curso</div>
            </div>
            <div class="kpi-card">
                <small>Balance neto</small>
                <strong style="color: {{ $balanceNetoMes >= 0 ? '#2E7D32' : '#C62828' }};">${{ number_format((float) $balanceNetoMes, 2) }}</strong>
                <div class="kpi-sub">Mes en curso</div>
            </div>
        @endif
        <div class="kpi-card">
            <small>Productos vendidos</small>
            <strong>{{ number_format((float) $productosVendidosMes, 2) }}</strong>
            <div class="kpi-sub">Unidades del mes</div>
        </div>
        <div class="kpi-card">
            <small>Apartados pendientes</small>
            <strong>{{ number_format((int) $apartadosPendientes) }}</strong>
            <div class="kpi-sub">Abiertos en general</div>
        </div>
    </div>

    {{-- Card: Precio del oro (hoy) - Mejorado --}}
    <div class="module-card gold-price-card" style="margin-bottom:2rem;padding:0;overflow:hidden;">
        <!-- Header del card -->
        <div style="background:linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);padding:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <div style="font-size:2.8rem;color:#1a1a1a;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h3 style="margin:0;color:#1a1a1a;font-size:1.4rem;font-weight:800;letter-spacing:0.5px;">
                        PRECIO DEL ORO
                    </h3>
                    <p style="margin:0;color:#2d2d2d;font-size:0.95rem;font-weight:600;opacity:0.9;">
                        <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            
            <select id="goldFilterSelect" class="form-control" style="max-width:180px;border:2px solid #1a1a1a;border-radius:8px;font-weight:700;padding:0.6rem;background:white;">
                <option value="all">Ver todos</option>
                @if(!empty($precios_oro_gramo) && count($precios_oro_gramo) > 0)
                    @foreach($precios_oro_gramo as $p)
                        <option value="{{ $p['k'] }}">{{ $p['k'] }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- Body del card con precios -->
        <div style="padding:1.8rem;background:white;">
            <div id="goldPricesGrid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;">
                @if(!empty($precios_oro_gramo) && count($precios_oro_gramo) > 0)
                    @foreach($precios_oro_gramo as $p)
                        <div class="gold-price-item" data-quilate="{{ $p['k'] }}" 
                             style="background:linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                                    border-radius:12px;
                                    padding:1.2rem;
                                    box-shadow:0 4px 12px rgba(0,0,0,0.08);
                                    text-align:center;
                                    transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                    border:2px solid transparent;
                                    cursor:pointer;">
                            <div style="font-size:0.9rem;color:#666;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.5rem;">
                                <i class="fas fa-gem" style="color:#D4AF37;margin-right:0.3rem;"></i>
                                {{ $p['k'] }}
                            </div>
                            <div style="font-size:1.3rem;color:#D4AF37;font-weight:800;letter-spacing:-0.5px;">
                                ${{ number_format((float) $p['v'], 2) }}
                            </div>
                            <div style="font-size:0.75rem;color:#999;font-weight:600;margin-top:0.3rem;">
                                MXN / GRAMO
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="grid-column:1/-1;text-align:center;padding:2rem;color:#999;">
                        <i class="fas fa-exclamation-circle" style="font-size:2rem;margin-bottom:0.5rem;"></i>
                        <p style="margin:0;font-weight:600;">No hay registros disponibles</p>
                    </div>
                @endif
            </div>

            <!-- Vista destacada cuando se selecciona uno -->
            <div id="goldSelectedView" style="margin-top:1.5rem;padding:1.5rem;background:linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);border-radius:12px;display:none;align-items:center;justify-content:space-between;">
                <div>
                    <div style="color:#D4AF37;font-weight:800;font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.5rem;">
                        Precio seleccionado
                    </div>
                    <div style="display:flex;align-items:baseline;gap:1rem;">
                        <span id="goldSelectedLabel" style="font-weight:800;color:white;font-size:1.8rem;"></span>
                        <span id="goldSelectedValue" style="font-weight:900;color:#D4AF37;font-size:2.2rem;"></span>
                    </div>
                </div>
                <button onclick="document.getElementById('goldFilterSelect').value='all';document.getElementById('goldFilterSelect').dispatchEvent(new Event('change'));" 
                        class="btn-module-access" style="background:white;color:#1a1a1a;padding:0.7rem 1.2rem;">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('goldFilterSelect');
    const items = document.querySelectorAll('.gold-price-item');
    const selectedView = document.getElementById('goldSelectedView');
    const selLabel = document.getElementById('goldSelectedLabel');
    const selValue = document.getElementById('goldSelectedValue');

    function resetItems() {
        items.forEach(i => {
            i.classList.remove('selected');
            i.style.display = 'block';
        });
        selectedView.style.display = 'none';
    }

    function highlightItem(quilate) {
        resetItems();
        let found = false;
        items.forEach(i => {
            if (i.dataset.quilate === quilate) {
                i.classList.add('selected');
                const priceText = i.querySelector('div:nth-child(2)').textContent.trim();
                selLabel.textContent = quilate;
                selValue.textContent = priceText;
                found = true;
            } else {
                i.style.display = 'none';
            }
        });
        if (found) {
            selectedView.style.display = 'flex';
        }
    }

    select && select.addEventListener('change', function(){
        const v = this.value;
        if (v === 'all') {
            resetItems();
        } else {
            highlightItem(v);
        }
    });

    // Click en item para seleccionarlo
    items.forEach(item => {
        item.addEventListener('click', function(){
            const quilate = this.dataset.quilate;
            select.value = quilate;
            select.dispatchEvent(new Event('change'));
        });
    });
});
</script>

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

