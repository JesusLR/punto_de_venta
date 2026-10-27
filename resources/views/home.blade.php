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
    :root {
        --primary-slate: #0f172a;
        --secondary-slate: #1e293b;
        --accent-gold: #D4AF37;
        --emerald-green: #10b981;
        --rose-red: #f43f5e;
        --blue-sky: #3b82f6;
        --purple-violet: #8b5cf6;
        --amber-orange: #f59e0b;
        --card-border: rgba(226, 232, 240, 0.8);
    }

    .home-container {
        padding: 2.5rem 0;
        max-width: 1240px;
        margin: 0 auto;
    }

    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, var(--primary-slate) 0%, var(--secondary-slate) 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        border-left: 5px solid var(--accent-gold);
        position: relative;
        overflow: hidden;
    }

    .welcome-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .welcome-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.5px;
    }

    .welcome-header .user-name {
        color: var(--accent-gold);
        text-shadow: 0 2px 10px rgba(212, 175, 55, 0.2);
    }

    .welcome-header .subtitle {
        font-size: 0.95rem;
        opacity: 0.85;
        margin: 0;
    }

    /* Wave Hand Animation */
    @keyframes wave {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(14deg); }
        20% { transform: rotate(-8deg); }
        30% { transform: rotate(14deg); }
        40% { transform: rotate(-4deg); }
        50% { transform: rotate(10deg); }
        60% { transform: rotate(0deg); }
        100% { transform: rotate(0deg); }
    }
    .animate-wave {
        display: inline-block;
        animation: wave 2.5s infinite;
        transform-origin: 70% 70%;
    }

    /* KPI Grid & Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .kpi-card-link {
        display: block;
        text-decoration: none !important;
        color: inherit;
        border-radius: 16px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .kpi-card-link:hover {
        transform: translateY(-5px);
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--card-border);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--kpi-color, var(--accent-gold));
        border-radius: 4px 0 0 4px;
    }

    .kpi-card-link:hover .kpi-card {
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
        border-color: rgba(226, 232, 240, 0.5);
    }

    .kpi-label {
        display: block;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .kpi-value {
        font-size: 1.6rem;
        color: #0f172a;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .kpi-sub {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .kpi-icon-container {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--kpi-bg, rgba(212, 175, 55, 0.1));
        color: var(--kpi-color, var(--accent-gold));
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .kpi-card-link:hover .kpi-icon-container {
        transform: scale(1.1);
    }

    /* Modules Grid */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .module-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .module-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08);
    }

    .module-icon-container {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.2rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
    }

    .module-icon-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .module-card:hover .module-icon-container::before {
        opacity: 1;
    }

    .module-card:hover .module-icon-container i {
        transform: scale(1.1) rotate(2deg);
    }

    .module-icon-container i {
        transition: transform 0.3s ease;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
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
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 1.2rem 0;
        text-align: center;
        letter-spacing: 0.2px;
    }

    .btn-module-access {
        background: linear-gradient(135deg, var(--secondary-slate) 0%, var(--primary-slate) 100%);
        color: var(--accent-gold);
        border: none;
        padding: 0.7rem 1.2rem;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }

    .btn-module-access:hover {
        background: linear-gradient(135deg, var(--primary-slate) 0%, var(--secondary-slate) 100%);
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
    }

    .gold-price-item.selected {
        border-color: var(--accent-gold) !important;
        background: linear-gradient(135deg, #ffffff 0%, rgba(212,175,55,0.05) 100%) !important;
        box-shadow: 0 8px 20px rgba(212,175,55,0.1) !important;
    }

    @media (max-width: 991px) {
        .welcome-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1.5rem;
        }
        .welcome-header .clock-display {
            text-align: left !important;
        }
    }

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }
        .welcome-header h1 {
            font-size: 1.8rem;
        }
    }
</style>

<div class="container home-container">
    <!-- Welcome Header Banner -->
    <div class="welcome-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1>
                <i class="fas fa-hand-sparkles text-warning mr-2 animate-wave"></i>
                ¡Bienvenido, <span class="user-name">{{ Auth::user()->name }}</span>!
            </h1>
            <p class="subtitle">
                <i class="far fa-calendar-alt mr-1"></i>
                <span id="liveDate"></span> · {{ env("APP_NAME") }}
            </p>
        </div>
        <div class="clock-display" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 0.6rem 1.4rem; color: #ffffff; text-align: right; box-shadow: 0 4px 15px rgba(0,0,0,0.15); min-width: 150px;">
            <div style="font-size: 1.35rem; font-weight: 800; letter-spacing: 0.5px;" id="liveClock">00:00:00</div>
            <div style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; opacity: 0.8; letter-spacing: 0.5px;">Hora del Sistema</div>
        </div>
    </div>

    <!-- KPIs Container -->
    <div class="kpi-grid">
        @if(Auth::user()->id == 1)
            <!-- Ingresos Automáticos -->
            <a href="{{ route('finanzas.index') }}" class="kpi-card-link" title="Ir a Finanzas">
                <div class="kpi-card" style="--kpi-color: var(--emerald-green); --kpi-bg: rgba(16, 185, 129, 0.1);">
                    <div class="d-flex justify-content-between align-items-start w-100">
                        <div class="flex-grow-1">
                            <small class="kpi-label">Ingresos</small>
                            <h3 class="kpi-value" style="color: #065f46;">${{ number_format((float) $ingresosAutomaticosMes, 2) }}</h3>
                            <div class="kpi-sub">Mes en curso</div>
                        </div>
                        <div class="kpi-icon-container">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Egresos Capturados -->
            <a href="{{ route('finanzas.index') }}" class="kpi-card-link" title="Ir a Finanzas">
                <div class="kpi-card" style="--kpi-color: var(--rose-red); --kpi-bg: rgba(244, 63, 94, 0.1);">
                    <div class="d-flex justify-content-between align-items-start w-100">
                        <div class="flex-grow-1">
                            <small class="kpi-label">Egresos</small>
                            <h3 class="kpi-value" style="color: #9f1239;">${{ number_format((float) $egresosCapturadosMes, 2) }}</h3>
                            <div class="kpi-sub">Mes en curso</div>
                        </div>
                        <div class="kpi-icon-container">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Balance Neto -->
            <a href="{{ route('finanzas.index') }}" class="kpi-card-link" title="Ir a Finanzas">
                <div class="kpi-card" style="--kpi-color: var(--blue-sky); --kpi-bg: rgba(59, 130, 246, 0.1);">
                    <div class="d-flex justify-content-between align-items-start w-100">
                        <div class="flex-grow-1">
                            <small class="kpi-label">Balance Neto</small>
                            <h3 class="kpi-value" style="color: {{ $balanceNetoMes >= 0 ? '#1e3a8a' : '#9f1239' }};">${{ number_format((float) $balanceNetoMes, 2) }}</h3>
                            <div class="kpi-sub">Mes en curso</div>
                        </div>
                        <div class="kpi-icon-container">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endif

        <!-- Productos Vendidos -->
        <a @if(Auth::user()->id == 1) href="{{ route('estadisticas.index') }}" @endif class="kpi-card-link" title="Ir a Estadísticas">
            <div class="kpi-card" style="--kpi-color: var(--purple-violet); --kpi-bg: rgba(139, 92, 246, 0.1);">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <div class="flex-grow-1">
                        <small class="kpi-label">Productos Vendidos</small>
                        <h3 class="kpi-value">{{ number_format((int) $productosVendidosMes, 0) }}</h3>
                        <div class="kpi-sub">Unidades del mes</div>
                    </div>
                    <div class="kpi-icon-container">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Apartados Pendientes -->
        <a href="{{ route('apartados.index') }}" class="kpi-card-link" title="Ir a Apartados">
            <div class="kpi-card" style="--kpi-color: var(--amber-orange); --kpi-bg: rgba(245, 158, 11, 0.1);">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <div class="flex-grow-1">
                        <small class="kpi-label">Apartados Abiertos</small>
                        <h3 class="kpi-value">{{ number_format((int) $apartadosPendientes) }}</h3>
                        <div class="kpi-sub">Abiertos en general</div>
                    </div>
                    <div class="kpi-icon-container">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Gold Price Widget Card -->
    <div class="card border-0 mb-4 shadow-sm" style="border-radius: 16px; overflow: hidden; background: white;">
        <div style="background: linear-gradient(135deg, var(--secondary-slate) 0%, var(--primary-slate) 100%); padding: 1.3rem 1.8rem; border-bottom: 2px solid var(--accent-gold);">
            <div class="row align-items-center justify-content-between">
                <div class="col-sm-8 mb-2 mb-sm-0 d-flex align-items-center" style="gap: 1rem;">
                    <div style="font-size: 1.8rem; color: var(--accent-gold); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: rgba(212,175,55,0.1); border-radius: 12px; border: 1px solid rgba(212,175,55,0.2);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; color: white; font-weight: 800; font-size: 1.25rem; letter-spacing: 0.3px;">COTIZADOR DE ORO</h4>
                        <p style="margin: 0; color: #94a3b8; font-size: 0.8rem; font-weight: 600;">Monitoreo de precios y tendencias (MXN por gramo)</p>
                    </div>
                </div>
                <div class="col-sm-4 d-flex justify-content-sm-end">
                    <select id="goldFilterSelect" class="form-control" style="border: 2px solid var(--accent-gold); border-radius: 10px; font-weight: 700; padding: 0.4rem 0.8rem; background: var(--secondary-slate); color: white; height: auto;">
                        <option value="all">Ver todos</option>
                        @if(!empty($precios_oro_gramo) && count($precios_oro_gramo) > 0)
                            @foreach($precios_oro_gramo as $p)
                                <option value="{{ $p['k'] }}">{{ $p['k'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row align-items-center">
                <!-- Prices grid -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div id="goldPricesGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem;">
                        @if(!empty($precios_oro_gramo) && count($precios_oro_gramo) > 0)
                            @foreach($precios_oro_gramo as $p)
                                <div class="gold-price-item" data-quilate="{{ $p['k'] }}" 
                                     style="background: #f8fafc;
                                            border-radius: 12px;
                                            padding: 1.2rem 1rem;
                                            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
                                            text-align: center;
                                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                            border: 2px solid transparent;
                                            cursor: pointer;">
                                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">
                                        <i class="fas fa-gem" style="color: var(--accent-gold); margin-right: 0.2rem;"></i>
                                        {{ $p['k'] }}
                                    </div>
                                    <div style="font-size: 1.4rem; color: #1e293b; font-weight: 800; letter-spacing: -0.5px;">
                                        ${{ number_format((float) $p['v'], 2) }}
                                    </div>
                                    <div style="font-size: 0.65rem; color: #94a3b8; font-weight: 700; margin-top: 0.3rem;">
                                        MXN / GRAMO
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: #94a3b8;">
                                <i class="fas fa-exclamation-circle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                <p style="margin: 0; font-weight: 600;">No hay cotizaciones del día</p>
                            </div>
                        @endif
                    </div>

                    <!-- Selected Karat View -->
                    <div id="goldSelectedView" class="mt-3 p-3 text-white" style="background: linear-gradient(135deg, var(--secondary-slate) 0%, var(--primary-slate) 100%); border-radius: 12px; display: none; align-items: center; justify-content: space-between; border-left: 4px solid var(--accent-gold);">
                        <div>
                            <div style="color: var(--accent-gold); font-weight: 800; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.2rem;">
                                Quilataje Seleccionado
                            </div>
                            <div class="d-flex align-items-baseline" style="gap: 0.5rem;">
                                <span id="goldSelectedLabel" style="font-weight: 800; color: white; font-size: 1.4rem;"></span>
                                <span id="goldSelectedValue" style="font-weight: 900; color: var(--accent-gold); font-size: 1.8rem; margin-left: 0.5rem;"></span>
                            </div>
                        </div>
                        <button onclick="document.getElementById('goldFilterSelect').value='all';document.getElementById('goldFilterSelect').dispatchEvent(new Event('change'));" 
                                class="btn btn-sm btn-outline-light px-3 py-1.5" style="border-radius: 8px; font-weight: 700; font-size: 0.8rem;">
                            <i class="fas fa-undo mr-1"></i> Restablecer
                        </button>
                    </div>
                </div>

                <!-- Trend chart -->
                <div class="col-lg-6">
                    <div style="background: #f8fafc; border-radius: 12px; padding: 1.2rem; border: 1px dashed #e2e8f0; height: 200px; position: relative;">
                        <canvas id="goldTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Interactive Charts Panel -->
    @if(Auth::user()->id == 1)
        <div class="row mb-4">
            <!-- Cash Flow Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 800; color: #0f172a;">Flujo de Caja</h5>
                            <small class="text-muted">Comparativa de ingresos y egresos (Últimos 15 días)</small>
                        </div>
                        <div class="kpi-icon-container" style="background: rgba(16, 185, 129, 0.1); color: var(--emerald-green); border-radius: 10px; width: 40px; height: 40px;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 280px; position: relative;">
                            <canvas id="cashFlowChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories sales chart -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; overflow: hidden; background: white;">
                    <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 800; color: #0f172a;">Por Categoría</h5>
                            <small class="text-muted">Venta en valor del mes actual</small>
                        </div>
                        <div class="kpi-icon-container" style="background: rgba(139, 92, 246, 0.1); color: var(--purple-violet); border-radius: 10px; width: 40px; height: 40px;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 280px; position: relative; display: flex; align-items: center; justify-content: center;">
                            @if(count($categoriasPopulares) > 0)
                                <canvas id="categoriesChart"></canvas>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-folder-open mb-2" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                                    <p class="mb-0">Sin transacciones registradas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Low Stock Alerts & Recent Sales -->
    <div class="row mb-5">
        <!-- Low Stock -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; background: white; overflow: hidden;">
                <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0" style="font-weight: 800; color: #0f172a;">Bajo Stock</h5>
                        <small class="text-muted">Productos con existencia crítica (3 o menos)</small>
                    </div>
                    <div class="kpi-icon-container" style="background: rgba(245, 158, 11, 0.1); color: var(--amber-orange); border-radius: 10px; width: 40px; height: 40px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(count($productosBajoStock) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px; width: 100%;">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                                        <th class="border-0 px-2 pb-2">Producto</th>
                                        <th class="border-0 pb-2 text-center">Existencia</th>
                                        <th class="border-0 pb-2 text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productosBajoStock as $prod)
                                        <tr class="align-middle" style="background: #f8fafc; border-radius: 8px;">
                                            <td class="px-3 py-3 border-0" style="border-radius: 12px 0 0 12px;">
                                                <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">{{ $prod->descripcion }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">Categoría: {{ $prod->Categorias->cNombreCategoria ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-center py-3 border-0">
                                                <span class="badge py-1.5 px-3" style="font-size: 0.75rem; font-weight: 800; background: {{ $prod->existencia <= 1 ? 'rgba(244, 63, 94, 0.1)' : 'rgba(245, 158, 11, 0.1)' }}; color: {{ $prod->existencia <= 1 ? 'var(--rose-red)' : 'var(--amber-orange)' }}; border-radius: 30px;">
                                                    {{ $prod->existencia }} uds
                                                </span>
                                            </td>
                                            <td class="text-end py-3 px-3 border-0" style="border-radius: 0 12px 12px 0;">
                                                <a href="{{ route('productos.editarProducto', $prod->id) }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-weight: 700; font-size: 0.75rem; border-color: #cbd5e1; color: #475569;">
                                                    <i class="fas fa-edit"></i> Surtir
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <div style="font-size: 2.8rem; color: var(--emerald-green); margin-bottom: 0.8rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <p class="mb-0" style="font-weight: 700; color: #475569;">¡Inventario saludable!</p>
                            <small class="text-muted">Todos los productos tienen existencias suficientes.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px; background: white; overflow: hidden;">
                <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0" style="font-weight: 800; color: #0f172a;">Ventas Recientes</h5>
                        <small class="text-muted">Últimas 5 ventas registradas</small>
                    </div>
                    <div class="kpi-icon-container" style="background: rgba(59, 130, 246, 0.1); color: var(--blue-sky); border-radius: 10px; width: 40px; height: 40px;">
                        <i class="fas fa-history"></i>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(count($ventasRecientes) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px; width: 100%;">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.8rem; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                                        <th class="border-0 px-2 pb-2">Cliente / Vendedor</th>
                                        <th class="border-0 pb-2 text-center">Método</th>
                                        <th class="border-0 pb-2 text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasRecientes as $v)
                                        <tr class="align-middle" style="background: #f8fafc; border-radius: 8px;">
                                            <td class="px-3 py-3 border-0" style="border-radius: 12px 0 0 12px;">
                                                <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">{{ $v['cliente'] }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">Por {{ $v['vendedor'] }} · {{ $v['fecha'] }}</small>
                                            </td>
                                            <td class="text-center py-3 border-0">
                                                <span class="badge py-1.5 px-3" style="font-size: 0.7rem; font-weight: 800; background: {{ $v['tipo_pago'] === 'MERCADO_PAGO' ? 'rgba(139, 92, 246, 0.1)' : 'rgba(16, 185, 129, 0.1)' }}; color: {{ $v['tipo_pago'] === 'MERCADO_PAGO' ? 'var(--purple-violet)' : 'var(--emerald-green)' }}; border-radius: 30px;">
                                                    {{ $v['tipo_pago'] }}
                                                </span>
                                            </td>
                                            <td class="text-end py-3 px-3 border-0" style="border-radius: 0 12px 12px 0; font-weight: 800; color: #0f172a; font-size: 0.95rem;">
                                                ${{ number_format($v['total'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <div style="font-size: 2.8rem; color: #cbd5e1; margin-bottom: 0.8rem;">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <p class="mb-0">Aún no hay transacciones registradas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modules / Quick Actions panel -->
    @php
        $modulos = Auth::user()->id == 1
            ? [
                ["nombre" => "productos", "color" => "linear-gradient(135deg, #FF6B6B 0%, #ff8787 100%)", "icono" => "fa-box"],
                ["nombre" => "estadisticas", "color" => "linear-gradient(135deg, #4ECDC4 0%, #63e2d9 100%)", "icono" => "fa-chart-bar"],
                ["nombre" => "ventas", "color" => "linear-gradient(135deg, #45B7D1 0%, #5ecfe8 100%)", "icono" => "fa-list"],
                ["nombre" => "vender", "color" => "linear-gradient(135deg, #FFA502 0%, #ffbe4f 100%)", "icono" => "fa-shopping-cart"],
                ["nombre" => "clientes", "color" => "linear-gradient(135deg, #20bf6b 0%, #26d076 100%)", "icono" => "fa-users"],
                ["nombre" => "usuarios", "color" => "linear-gradient(135deg, #C44569 0%, #dc5c80 100%)", "icono" => "fa-user-tie"],
            ]
            : [
                ["nombre" => "productos", "color" => "linear-gradient(135deg, #FF6B6B 0%, #ff8787 100%)", "icono" => "fa-box"],
                ["nombre" => "ventas", "color" => "linear-gradient(135deg, #45B7D1 0%, #5ecfe8 100%)", "icono" => "fa-list"],
                ["nombre" => "vender", "color" => "linear-gradient(135deg, #FFA502 0%, #ffbe4f 100%)", "icono" => "fa-shopping-cart"],
            ];
    @endphp

    <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; letter-spacing: -0.3px;">
        <i class="fas fa-th-large mr-2" style="color: var(--accent-gold);"></i>Accesos Directos
    </h4>

    <div class="modules-grid mb-5">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        // Hora
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById('liveClock');
        if (clockEl) clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        
        // Fecha
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateFormatted = now.toLocaleDateString('es-ES', options);
        const dateEl = document.getElementById('liveDate');
        if (dateEl) dateEl.textContent = dateFormatted.charAt(0).toUpperCase() + dateFormatted.slice(1);
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Gráfico de Tendencia de Precios del Oro
    const goldCtx = document.getElementById('goldTrendChart');
    if (goldCtx) {
        const oroHistorico = @json($oroHistorico);
        window.goldChart = new Chart(goldCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: oroHistorico.map(x => x.fecha),
                datasets: [
                    {
                        label: '24k',
                        data: oroHistorico.map(x => x.price_24k),
                        borderColor: '#D4AF37',
                        backgroundColor: 'rgba(212, 175, 55, 0.08)',
                        borderWidth: 2.5,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '14k',
                        data: oroHistorico.map(x => x.price_14k),
                        borderColor: '#45B7D1',
                        backgroundColor: 'rgba(69, 183, 209, 0.08)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        hidden: false
                    },
                    {
                        label: '10k',
                        data: oroHistorico.map(x => x.price_10k),
                        borderColor: '#FF6B6B',
                        backgroundColor: 'rgba(255, 107, 107, 0.08)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        hidden: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                            boxWidth: 10,
                            padding: 8
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        ticks: {
                            callback: function(value) { return '$' + value; },
                            font: { size: 9 }
                        }
                    }
                }
            }
        });
    }

    // 3. Filtrado de Oro Interactivo
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

        if (window.goldChart) {
            window.goldChart.data.datasets.forEach(dataset => {
                dataset.hidden = false;
            });
            window.goldChart.update();
        }
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

        if (window.goldChart) {
            window.goldChart.data.datasets.forEach(dataset => {
                if (dataset.label.toLowerCase() === quilate.toLowerCase()) {
                    dataset.hidden = false;
                } else {
                    dataset.hidden = true;
                }
            });
            window.goldChart.update();
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

    // 4. Gráficos de Administrador
    @if(Auth::user()->id == 1)
        // Flujo de caja
        const cashFlowCtx = document.getElementById('cashFlowChart');
        if (cashFlowCtx) {
            const dailyStats = @json($dailyStats);
            new Chart(cashFlowCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: dailyStats.map(x => x.fecha),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: dailyStats.map(x => x.ingresos),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            borderWidth: 3,
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Egresos',
                            data: dailyStats.map(x => x.egresos),
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            borderWidth: 3,
                            tension: 0.35,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: 'Plus Jakarta Sans', weight: 'bold' }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) { return '$' + value; }
                            }
                        }
                    }
                }
            });
        }

        // Categorías más vendidas
        @if(count($categoriasPopulares) > 0)
            const categoriesCtx = document.getElementById('categoriesChart');
            if (categoriesCtx) {
                const categoriesData = @json($categoriasPopulares);
                new Chart(categoriesCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: categoriesData.map(x => x.categoria),
                        datasets: [{
                            data: categoriesData.map(x => x.total),
                            backgroundColor: [
                                '#8b5cf6', // Violet
                                '#3b82f6', // Blue
                                '#f59e0b', // Amber
                                '#ec4899', // Pink
                                '#14b8a6'  // Teal
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                                    boxWidth: 10,
                                    padding: 8
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        @endif
    @endif
});
</script>

@endsection
