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
@section("titulo", "Clientes")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

    <div class="row">
        <div class="col-12">
            <div class="productos-header">
                <h1><i class="fa fa-users"></i> Clientes</h1>
                @if(Auth::user()->hasPermission('manage_clients'))
                    <div class="header-actions">
                        <a href="{{route("clientes.create")}}" class="btn-modern btn-success-modern">
                            <i class="fas fa-plus"></i> Agregar cliente
                        </a>
                    </div>
                @endif
            </div>
            @include("notificacion")
            <div class="table-container">
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                @if(Auth::user()->hasPermission('manage_clients'))
                                    <th>Editar</th>
                                @endif
                                @if(Auth::user()->hasPermission('delete_clients'))
                                    <th>Eliminar</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clientes as $cliente)
                                <tr>
                                    <td>{{$cliente->nombre}}</td>
                                    <td>{{$cliente->telefono}}</td>
                                    @if(Auth::user()->hasPermission('manage_clients'))
                                        <td>
                                            <a class="btn btn-warning btn-table-action" href="{{route("clientes.edit",[$cliente])}}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    @endif
                                    @if(Auth::user()->hasPermission('delete_clients'))
                                        <td>
                                            <form action="{{route("clientes.destroy", [$cliente])}}" method="post">
                                                @method("delete")
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-table-action">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
