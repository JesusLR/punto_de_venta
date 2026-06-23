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
@section("titulo", "Usuarios")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

    <div class="row">
        <div class="col-12">
            <div class="productos-header">
                <h1><i class="fa fa-users"></i> Usuarios</h1>
                <div class="header-actions">
                    <a href="{{route("roles.index")}}" class="btn-modern" style="border-color: #D4AF37;">
                        <i class="fas fa-user-shield"></i> Roles y Permisos
                    </a>
                    <a href="{{route("usuarios.create")}}" class="btn-modern btn-success-modern">
                        <i class="fas fa-plus"></i> Agregar usuario
                    </a>
                </div>
            </div>
            @include("notificacion")
            <div class="table-container">
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Correo electrónico</th>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Editar</th>
                                <th>Eliminar</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{$usuario->email}}</td>
                                    <td>{{$usuario->name}}</td>
                                    <td>
                                        @if($usuario->role)
                                            <span class="badge badge-primary" style="font-size: 0.85rem; padding: 0.35rem 0.75rem; border-radius: 12px; background-color: #3b82f6;">
                                                {{$usuario->role->name}}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary" style="font-size: 0.85rem; padding: 0.35rem 0.75rem; border-radius: 12px; background-color: #94a3b8;">
                                                Sin Rol
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-warning btn-table-action" href="{{route("usuarios.edit",[$usuario])}}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{route("usuarios.destroy", [$usuario])}}" method="post">
                                            @method("delete")
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-table-action">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
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
