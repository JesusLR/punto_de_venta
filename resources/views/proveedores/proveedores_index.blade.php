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
@section("titulo", "Proveedores")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<input type="text" id="userID" value={{Auth::user()->id}} hidden>
<input type="text" id="iIDProveedor" hidden>
    <div class="row">
        <div class="col-12">
            <div class="productos-header">
                <h1><i class="fas fa-truck"></i> Proveedores</h1>
                @if (Auth::user()->id == 1)
                    <div class="header-actions">
                        <a href="#" id="btnAgregarProveedor" class="btn-modern btn-success-modern" title="Agregar Proveedor">
                            <i class="fas fa-plus"></i> Agregar proveedor
                        </a>
                    </div>
                @endif
            </div>
            @include("notificacion")

            {{-- <div class="form-group">
                <label for="id_cliente">Cliente</label>
                <select required class="form-control" name="" id="">
                        <option value="T">Todos</option>
                        <option value="F">Sin foto</option>
                        <option value="B" style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">Blancos</option>
                        <option value="R" style="background-color: red; color: white;">Rojo</option>
                        <option value="N" style="background-color: orange; color: white;">Naranja</option>
                </select>
            </div> --}}

            <div class="table-container">
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="gridProveedores">
                            {{-- <thead>
                            <tr>
                                <th>Código de barras</th>
                                <th>Descripción</th>
                                <th>Precio de compra</th>
                                <th>Precio de venta</th>
                                <th>Utilidad</th>
                                <th>Existencia</th>
                                <th>Imagen</th>
                                @if (Auth::user()->id == 1)
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($productos as $producto)
                                <tr>
                                    <td>{{$producto->codigo_barras}}</td>
                                    <td>{{$producto->descripcion}}</td>
                                    <td>{{$producto->precio_compra}}</td>
                                    <td>{{$producto->precio_venta}}</td>
                                    <td>{{$producto->precio_venta - $producto->precio_compra}}</td>
                                    <td>{{$producto->existencia}}</td>
                                    <td><img src="img/productos/{{$producto->img}}" alt="{{$producto->img}}" width="100" height="100"></td>
                                    @if (Auth::user()->id == 1)
                                        <td>
                                            <a class="btn btn-warning" href="{{route("productos.edit",[$producto])}}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{route("productos.destroy", [$producto])}}" method="post">
                                                @method("delete")
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('proveedores.modals.formProveedor')

    <script src="{{ asset('js/plugins/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script src="{{asset('js/proveedores.js')}}"></script>

@endsection
