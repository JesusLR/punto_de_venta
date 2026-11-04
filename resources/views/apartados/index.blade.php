@extends("maestra")
@section("titulo", "Gestión de Apartados")
@section("contenido")


<input type="text" id="userID" value="{{Auth::user()->id}}" hidden>
<input type="text" id="iIDApartado" hidden>

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <div>
                <span class="workspace-eyebrow">Seguimiento de clientes</span>
                <h1>Apartados y abonos</h1>
                <p class="workspace-description">Cada pago, en orden. Consulta saldos, registra abonos y da seguimiento a tus apartados.</p>
            </div>
            <span class="workspace-header-icon" aria-hidden="true"><i class="fas fa-receipt"></i></span>
        </div>

        @include("notificacion")

        <div class="filters-container">
            <div class="workspace-section-heading"><i class="fas fa-sliders-h" aria-hidden="true"></i> Encuentra un apartado</div>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="cTipoBusquedaApartado">Cliente</label>
                    <select required class="form-control-modern" name="cTipoBusquedaApartado" id="cTipoBusquedaApartado">
                        <option value="T">Todos</option>
                        @foreach ($clientes as $cliente)
                        <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="cEstadoApartado">Estado</label>
                    <select class="form-control-modern" name="cEstadoApartado" id="cEstadoApartado">
                        <option value="T">Todos</option>
                        <option value="ABIERTO">Abierto</option>
                        <option value="LIQUIDADO">Liquidado</option>
                        <option value="CANCELADO">Cancelado</option>
                    </select>
                </div>

                {{-- <div class="col-md-3 mb-3">
                    <label for="cFechaInicioApartado">Fecha inicio</label>
                    <input type="date" class="form-control-modern" name="cFechaInicioApartado" id="cFechaInicioApartado">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="cFechaFinApartado">Fecha fin</label>
                    <input type="date" class="form-control-modern" name="cFechaFinApartado" id="cFechaFinApartado">
                </div> --}}
            </div>
        </div>

        <div class="table-container">
            <div class="workspace-table-heading"><div><h2>Control de apartados</h2><p>Abre el historial para consultar los movimientos de cada cliente.</p></div><span><i class="fas fa-circle" aria-hidden="true"></i> Saldos y movimientos</span></div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-bordered" id="gridApartados"></table>
                </div>
            </div>
        </div>
    </div>
</div>

    @include('apartados.modals.abono')
    @include('apartados.modals.productos_body')
    @include('apartados.modals.historial_abonos_body')
    @include('apartados.modals.nombre_apartado')

<script src="{{ asset('js/apartados.js') }}?v={{ filemtime(public_path('js/apartados.js')) }}"></script>

@endsection
