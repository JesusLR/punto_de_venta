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
@section("titulo", "Ventas")
@section("contenido")
<input type="text" id="userID" value={{Auth::user()->id}} hidden>
<input type="text" id="iIDVenta" hidden>
    <div class="row">
        <div class="col-12">
            <h1>Ventas <i class="fa fa-list"></i></h1>
            @include("notificacion")

            <div class="row" @if (Auth::user()->id != 1) style="display: none;"  @endif>
                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaProductos">Usuario</label>
                    <select required class="form-control" name="cTipoBusquedaVenta" id="cTipoBusquedaVenta">
                        <option value="T">Todos</option>
                        @foreach ($users as $user)
                        <option @if ($user->id != 1 && Auth::user()->id == $user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="gridVentas">
                    {{-- <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Ticket de venta</th>
                        <th>Detalles</th>
                        @if (Auth::user()->id == 1)
                            <th>Eliminar</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ventas as $venta)
                        <tr>
                            <td>{{$venta->created_at}}</td>
                            <td>{{$venta->cliente->nombre}}</td>
                            <td>${{number_format($venta->total, 2)}}</td>
                            <td>
                                <a class="btn btn-info" href="{{route("ventas.ticket", ["id"=>$venta->id])}}">
                                    <i class="fa fa-print"></i>
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-success" href="{{route("ventas.show", $venta)}}">
                                    <i class="fa fa-info"></i>
                                </a>
                            </td>
                            @if (Auth::user()->id == 1)
                                <td>
                                    <form action="{{route("ventas.destroy", [$venta])}}" method="post">
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

    @include('ventas.modals.editNombreVentaModal')

    <script src="{{asset('js/ventas.js')}}"></script>

@endsection
