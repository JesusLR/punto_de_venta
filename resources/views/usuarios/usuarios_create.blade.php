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
@section("titulo", "Agregar usuario")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

    <div class="row">
        <div class="col-12">
            <div class="productos-header">
                <h1><i class="fas fa-user-plus"></i> Agregar usuario</h1>
            </div>

            <form method="POST" action="{{route("usuarios.store")}}" class="form-container">
                @csrf
                <div class="section-title">
                    <i class="fas fa-user-shield"></i>
                    Datos del usuario
                </div>

                <div class="form-group-modern">
                    <label for="name"><i class="fas fa-user"></i>Nombre</label>
                    <input id="name" required autocomplete="off" name="name" class="form-control form-control-modern" type="text" placeholder="Nombre">
                </div>
                <div class="form-group-modern">
                    <label for="email"><i class="fas fa-envelope"></i>Correo electrónico</label>
                    <input id="email" required autocomplete="off" name="email" class="form-control form-control-modern" type="email" placeholder="Correo electrónico">
                </div>
                <div class="form-group-modern">
                    <label for="password"><i class="fas fa-lock"></i>Contraseña</label>
                    <input id="password" required autocomplete="off" name="password" class="form-control form-control-modern" type="password" placeholder="Contraseña">
                </div>

                @include("notificacion")
                <button class="btn-action btn-success-modern"><i class="fas fa-save"></i> Guardar</button>
                <a class="btn-action btn-secondary" href="{{route("usuarios.index")}}"><i class="fas fa-arrow-left"></i> Volver al listado</a>
            </form>
        </div>
    </div>
@endsection
