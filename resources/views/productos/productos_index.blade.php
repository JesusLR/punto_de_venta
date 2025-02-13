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
@section("titulo", "Productos")
@section("contenido")
<input type="text" id="userID" value={{Auth::user()->id}} hidden>
    <div class="row">
        <div class="col-12">
            <h1>Productos <i class="fa fa-box"></i></h1>
            @if (Auth::user()->id == 1)
                <a href="{{route("productos.create")}}" class="btn btn-success mb-2"  title="Agregar Producto"><i class="fas fa-plus"></i> Agregar</a>
                <button class="btn btn-primary mb-2" id="btnCargaExcell" title="Cargar Productos"><i class="fas fa-file-excel"></i> Cargar Productos</button>
                <a href="{{ asset('docs/plantilla_productos.xlsx') }}" class="btn btn-info mb-2" title="Descargar plantilla de excell"><i class="fas fa-file-download"></i> Descargar Plantilla</a>

                {{-- <span id="btnImgISAIspan"><a target='_blank' href="{{ asset('docs/plantilla_productos.xlsx') }}" tabindex="-1" id="" data-toggle="tooltip" title="Descargar plantilla de excell"><i class="fas fa-file-download"></i></a></span> --}}
                {{-- <a  class="btn btn-primary mb-2"> <i class="fas fa-file-excel"></i> Cargar Productos</a> --}}
            @endif
            @include("notificacion")

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaProductos">Dsiponibilidad</label>
                    <select required class="form-control" name="cTipoBusquedaProductos" id="cTipoBusquedaProductos">
                        <option value="T">Todos</option>
                        <option value="F">Sin foto</option>
                        <option value="B" style="background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);">Blancos</option>
                        <option value="R" style="background-color: red; color: white;">Rojo</option>
                        <option value="N" style="background-color: orange; color: white;">Naranja</option>
                    </select>
                </div>
            
                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaProveedor">Proveedor</label>
                    <select required class="form-control" name="cTipoBusquedaProveedor" id="cTipoBusquedaProveedor">
                        <option value="T">Todos</option>
                        <option value="0">N/A</option>
                        @foreach ($lstProveedores as $proveedor)
                            <option value="{{$proveedor->id}}">{{$proveedor->cNombreProveedor}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaMaterial">Material</label>
                    <select required class="form-control" name="cTipoBusquedaMaterial" id="cTipoBusquedaMaterial">
                        <option value="T">Todos</option>
                        <option value="0">N/A</option>
                        @foreach ($lstMateriales as $material)
                            <option value="{{$material->id}}">{{$material->cNombreMaterial}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaCategoria">Categoria</label>
                    <select required class="form-control" name="cTipoBusquedaCategoria" id="cTipoBusquedaCategoria">
                        <option value="T">Todos</option>
                        <option value="0">N/A</option>
                        @foreach ($lstCategorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->cNombreCategoria}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="gridProductos">
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

    @include('productos.modals.cargaExcell')
    @include('productos.modals.imagen')

    <script src="{{asset('js/productos.js')}}"></script>

@endsection
