@extends("maestra")
@section("titulo", "Estadisticas")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Estadisticas <i class="fas fa-chart-pie"></i></h1>
            <!-- <a href="{{route("usuarios.create")}}" class="btn btn-success mb-2">Agregar</a> -->
            @include("notificacion")

            <div class="row">
                <div class="col-md-2">
                    <span class="input-group-addon">
                        Fecha inicio:
                    </span>
                    <input autocomplete="off" class="form-control" id="dtFechaInicio" name="dtFechaInicio" type="date"/>
                </div>
                <div class="col-md-2">
                    <span class="input-group-addon">
                        Fecha final:
                    </span>
                    <input autocomplete="off" class="form-control" id="dtFechaFinal" name="dtFechaFinal" type="date"/>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button class="btn btn-primary mb-2" id="btnBuscarEstadistica" title="Buscar"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </div>

            <canvas id="myChart" width="300" height="150"></canvas>

        </div>
    </div>

    <script src="{{asset('js/estadisticas.js')}}"></script>
@endsection
