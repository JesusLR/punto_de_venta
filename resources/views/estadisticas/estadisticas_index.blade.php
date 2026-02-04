@extends("maestra")
@section("titulo", "Estadisticas")
@section("contenido")

<style>
    .estadisticas-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border-left: 4px solid #D4AF37;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .estadisticas-header h1 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .estadisticas-header i {
        font-size: 2.5rem;
        margin-left: 1rem;
        color: #D4AF37;
    }

    .filters-container {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .filters-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2a2a2a;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filters-title i {
        color: #D4AF37;
        font-size: 1.3rem;
    }

    .filter-group {
        margin-bottom: 1.5rem;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-group label {
        display: block;
        font-weight: 600;
        color: #2a2a2a;
        margin-bottom: 0.6rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .filter-group input[type="date"] {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 0.95rem;
        background-color: white;
        color: #2a2a2a;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-group input[type="date"]:focus {
        outline: none;
        border-color: #D4AF37;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr auto;
        gap: 1.5rem;
        align-items: flex-end;
    }

    .filter-row-dates {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1.5rem;
        align-items: flex-end;
        margin-bottom: 1.5rem;
    }

    .filter-row-selects {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .filter-row-selects .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-row-selects .filter-group label {
        height: 2.6rem;
        display: flex;
        align-items: center;
        margin-bottom: 0;
    }

    .filter-row-selects .form-control {
        width: 100%;
    }

    .filter-group select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 1rem;
        background-color: white;
        color: #2a2a2a;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        height: auto;
        min-height: 42px;
        line-height: 1.5;
    }

    .filter-group select:focus {
        outline: none;
        border-color: #D4AF37;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
    }

    .filter-group select option {
        padding: 0.5rem;
        background-color: white;
        color: #2a2a2a;
    }

    .btn-search {
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        color: #D4AF37;
        border: 1px solid #D4AF37;
        padding: 0.8rem 2rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .btn-search:hover {
        background: #D4AF37;
        color: white;
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }

    .chart-container {
        background: white;
        padding: 1rem; /* reducido */
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.25rem;
    }

    .chart-title {
        font-size: 1rem; /* más pequeño */
        font-weight: 700;
        color: #2a2a2a;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: space-between;
    }

    #myChart {
        display: block;
        margin: 0 auto;
        width: 100% !important;
        height: 300px; /* reducir altura */
        max-height: 360px;
    }

    @media (min-width: 1200px) {
        #myChart { height: 320px; }
    }

    @media (max-width: 768px) {
        .estadisticas-header {
            flex-direction: column;
            text-align: center;
        }

        .estadisticas-header h1 {
            font-size: 1.8rem;
        }

        .filter-row-dates {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .filter-row-selects {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .filters-container {
            padding: 1.5rem 1rem;
        }

        .filter-group {
            margin-bottom: 1rem;
        }

        .chart-container {
            padding: 1.5rem 1rem;
        }
    }

    @media (max-width: 600px) {
        .estadisticas-header {
            padding: 1.5rem 1rem;
        }

        .estadisticas-header h1 {
            font-size: 1.5rem;
        }

        .btn-search {
            padding: 0.6rem 1.2rem;
            font-size: 0.85rem;
        }

        #myChart {
            width: 100% !important;
            height: auto !important;
        }

        .filter-row-dates {
            grid-template-columns: 1fr;
        }

        .filter-row-selects {
            grid-template-columns: 1fr;
        }
    }
</style>

    <div class="row">
        <div class="col-12">
            <div class="estadisticas-header">
                <div>
                    <h1>
                        <i class="fas fa-chart-pie"></i>
                            Estadísticas
                    </h1>
                </div>
            </div>

            

            @include("notificacion")

            <div class="filters-container">
                <div class="filters-title">
                    <i class="fas fa-filter"></i> Filtros de búsqueda
                </div>
                
                <!-- Fila 1: Fechas y Botón Buscar -->
                <div class="filter-row-dates">
                    <div class="filter-group">
                        <label for="dtFechaInicio"><i class="fas fa-calendar-alt" style="color: #D4AF37; margin-right: 0.5rem;"></i>Fecha Inicio</label>
                        <input autocomplete="off" class="form-control" id="dtFechaInicio" name="dtFechaInicio" type="date"/>
                    </div>
                    <div class="filter-group">
                        <label for="dtFechaFinal"><i class="fas fa-calendar-alt" style="color: #D4AF37; margin-right: 0.5rem;"></i>Fecha Final</label>
                        <input autocomplete="off" class="form-control" id="dtFechaFinal" name="dtFechaFinal" type="date"/>
                    </div>
                    <button class="btn-search" id="btnBuscarEstadistica" title="Buscar estadísticas"><i class="fas fa-search"></i> Buscar</button>
                </div>

                <!-- Fila 2: Tipo de Gráfico y Registros -->
                <div class="filter-row-selects">
                    <div class="filter-group">
                        <label for="cTipoGrafica"><i class="fas fa-chart-line" style="color: #D4AF37; margin-right: 0.5rem;"></i>Tipo Gráfico</label>
                        <select class="form-control" id="cTipoGrafica" name="cTipoGrafica">
                            <option value="bar">Barras</option>
                            <option value="line">Líneas</option>
                            <option value="doughnut">Donut</option>
                            <option value="pie">Pastel</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="cLimiteRegistros"><i class="fas fa-list" style="color: #D4AF37; margin-right: 0.5rem;"></i>Registros a Mostrar</label>
                        <select class="form-control" id="cLimiteRegistros" name="cLimiteRegistros">
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                            <option value="30">Top 30</option>
                            <option value="50">Top 50</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-bar-chart"></i> Gráfico de Ventas
                </div>
                <canvas id="myChart" style="width:100%;height:420px;"></canvas>
            </div>
        </div>
    </div>

    <script src="{{asset('js/estadisticas.js')}}"></script>
@endsection

