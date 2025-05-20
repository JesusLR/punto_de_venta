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

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-5px);
        transition: all 0.3s ease-in-out;
    }

    .transition {
        transition: all 0.3s ease-in-out;
    }

</style>

<div class="container py-4">
    <div class="text-center mb-5">
        <h1 class="display-4">Bienvenido, <strong>{{ Auth::user()->name }}</strong></h1>
    </div>

    @php
        $modulos = Auth::user()->id == 1
            ? [["productos", "estadisticas", "ventas", "vender", "clientes", "usuarios"]]
            : [["productos", "ventas", "vender"]];
    @endphp

<div class="card card h-100 shadow-sm rounded hover-shadow transition mb-3" style="max-width: 540px;">
    <div class="row no-gutters">
      <div class="col-md-4" style="height: 100%;">
        <img src="{{ asset('img/ganancias.png') }}" alt="..." style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div class="col-md-8">
        <div class="card-body">
          <h5 class="card-title">Card title</h5>
          <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
          <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
        </div>
      </div>
    </div>
  </div>
  

    @foreach($modulos as $fila)
        <div class="row justify-content-center mb-4">
            @foreach($fila as $modulo)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm rounded hover-shadow transition">
                        <a href="{{ route("$modulo.index") }}">
                            <img src="{{ url("/img/$modulo.png") }}" class="card-img-top" alt="{{ $modulo }}">
                        </a>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title text-center text-capitalize">
                                {{ $modulo === "acerca_de" ? "Acerca de" : str_replace('_', ' ', ucfirst($modulo)) }}
                            </h5>
                            <a href="{{ route("$modulo.index") }}" class="btn btn-success mt-3 btn-block">
                                <i class="fa fa-arrow-right mr-2"></i>
                                Ir a {{ $modulo === "acerca_de" ? "Acerca de" : str_replace('_', ' ', ucfirst($modulo)) }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection

