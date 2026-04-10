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

    .table-container-estadisticas {
        background: white;
        padding: 1rem;
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

    .resumen-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .resumen-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1rem;
        border-left: 4px solid #D4AF37;
    }

    .resumen-card small {
        display: block;
        color: #666;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .resumen-card strong {
        font-size: 1.25rem;
        color: #1a1a1a;
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

        .resumen-grid {
            grid-template-columns: 1fr;
        }

        .filters-container {
            padding: 1.5rem 1rem;
        }

        .filter-group {
            margin-bottom: 1rem;
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

                <!-- Fila 2: Filtros -->
                <div class="filter-row-selects">
                    <div class="filter-group">
                        <label for="cProducto"><i class="fas fa-box" style="color: #D4AF37; margin-right: 0.5rem;"></i>Producto</label>
                        <select class="form-control" id="cProducto" name="cProducto">
                            <option value="T">Todos</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="cFiltroUnidades"><i class="fas fa-cubes" style="color: #D4AF37; margin-right: 0.5rem;"></i>Unidades vendidas</label>
                        <select class="form-control" id="cFiltroUnidades" name="cFiltroUnidades">
                            <option value="RANGO">En el rango de fechas</option>
                            <option value="GENERAL">General (histórico)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="resumen-grid">
                <div class="resumen-card">
                    <small>Productos vendidos</small>
                    <strong id="txtTotalProductos">0</strong>
                </div>
                <div class="resumen-card">
                    <small>Unidades vendidas</small>
                    <strong id="txtTotalUnidades">0</strong>
                </div>
                <div class="resumen-card">
                    <small>Total vendido</small>
                    <strong id="txtTotalVendido">$0.00</strong>
                </div>
            </div>

            <div class="table-container-estadisticas">
                <div class="chart-title">
                    <i class="fas fa-table"></i> Detalle de productos vendidos
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="gridDetalleVentas">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Código de barras</th>
                                <th>Producto</th>
                                <th class="text-right">Unidades vendidas</th>
                                <th class="text-right">Precio promedio</th>
                                <th class="text-right">Total vendido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Realiza una búsqueda para ver resultados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('js/estadisticas.js')}}"></script>
@endsection

