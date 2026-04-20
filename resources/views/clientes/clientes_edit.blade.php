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
@extends("maestra")
@section("titulo", "Editar cliente")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

    <div class="row">
        <div class="col-12">
            <div class="productos-header">
                <h1><i class="fas fa-user-edit"></i> Editar cliente</h1>
            </div>

            <form method="POST" action="{{route("clientes.update", [$cliente])}}" class="form-container">
                @method("PUT")
                @csrf
                <div class="section-title">
                    <i class="fas fa-id-card"></i>
                    Datos del cliente
                </div>

                <div class="form-group-modern">
                    <label for="nombre"><i class="fas fa-user"></i>Nombre</label>
                    <input id="nombre" required value="{{$cliente->nombre}}" autocomplete="off" name="nombre" class="form-control form-control-modern" type="text" placeholder="Nombre">
                </div>
                <div class="form-group-modern">
                    <label for="telefono"><i class="fas fa-phone"></i>Teléfono</label>
                    <input id="telefono" required value="{{$cliente->telefono}}" autocomplete="off" name="telefono" class="form-control form-control-modern" type="text" placeholder="Teléfono">
                </div>

                @include("notificacion")
                <button class="btn-action btn-success-modern"><i class="fas fa-save"></i> Guardar</button>
                <a class="btn-action btn-secondary" href="{{route("clientes.index")}}"><i class="fas fa-arrow-left"></i> Volver</a>
            </form>
        </div>
    </div>
@endsection
