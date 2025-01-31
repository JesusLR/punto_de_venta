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
@section("titulo", "Editar producto")
@section("contenido")
    <div class="row">
        <div class="col-12">
            <h1>Editar producto</h1>
            <form method="POST" action="{{route("productos.update", [$producto])}}" enctype="multipart/form-data">
                @method("PUT")
                @csrf
                <div class="form-group">
                    <label class="label">Código de barras</label>
                    <input required value="{{$producto->codigo_barras}}" autocomplete="off" name="codigo_barras"
                           class="form-control"
                           type="text" placeholder="Código de barras">
                </div>
                <div class="form-group">
                    <label class="label">Descripción</label>
                    <input required value="{{$producto->descripcion}}" autocomplete="off" name="descripcion"
                           class="form-control"
                           type="text" placeholder="Descripción">
                </div>
                <div class="form-group">
                    <label class="label">Precio de compra</label>
                    <input required value="{{$producto->precio_compra}}" autocomplete="off" name="precio_compra"
                           class="form-control"
                           type="decimal(9,2)" placeholder="Precio de compra">
                </div>
                <div class="form-group">
                    <label class="label">Precio de venta</label>
                    <input required value="{{$producto->precio_venta}}" autocomplete="off" name="precio_venta"
                           class="form-control"
                           type="decimal(9,2)" placeholder="Precio de venta">
                </div>
                <div class="form-group">
                    <label class="label">Existencia</label>
                    <input required value="{{$producto->existencia}}" autocomplete="off" name="existencia"
                           class="form-control"
                           type="decimal(9,2)" placeholder="Existencia">
                </div>

                <div class="form-group">
                    <label class="label">Proveedor</label>
                    <select name="id_proveedor" id="id_proveedor" class="form-control">
                        <option value="0">Seleccione un proveedor</option>
                        @foreach($lstProveedores as $proveedor)
                            <option @if($proveedor->id == $producto->id_proveedor) selected @endif value="{{ $proveedor->id }}">{{ $proveedor->cNombreProveedor }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">Categoria</label>
                    <select name="id_categoria" id="id_categoria" class="form-control">
                        <option value="0">Seleccione una categoria</option>
                        @foreach($lstCategorias as $categoria)
                            <option @if($categoria->id == $producto->id_categoria) selected @endif value="{{ $categoria->id }}">{{ $categoria->cNombreCategoria }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="label">Material</label>
                    <select name="id_material" id="id_material" class="form-control">
                        <option value="0">Seleccione un material</option>
                        @foreach($lstMateriales as $material)
                            <option @if($material->id == $producto->id_material) selected @endif value="{{ $material->id }}">{{ $material->cNombreMaterial }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="exampleFormControlFile1">Imagen</label>
                    <input type="file" class="form-control-file" id="img" name="img">
                </div>

                @include("notificacion")
                <button class="btn btn-success">Guardar</button>
                <a class="btn btn-primary" href="{{route("productos.index")}}">Volver</a>
            </form>
        </div>
    </div>
@endsection
