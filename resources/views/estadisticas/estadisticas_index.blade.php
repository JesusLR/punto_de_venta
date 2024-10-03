@extends("maestra")
@section("titulo", "Estadisticas")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Estadisticas <i class="fas fa-chart-pie"></i></h1>
            <!-- <a href="{{route("usuarios.create")}}" class="btn btn-success mb-2">Agregar</a> -->
            @include("notificacion")
            
            <table class="table table-bordered" id="gridEstadisticas">


        </div>
    </div>
@endsection