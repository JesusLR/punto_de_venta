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

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<input type="text" id="userID" value="{{Auth::user()->id}}" hidden>
<input type="text" id="iIDVenta" hidden>

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <h1><i class="fa fa-list"></i> Ventas</h1>
        </div>

        @include("notificacion")

        <div class="filters-container">
            <div class="row" @if (Auth::user()->id != 1) style="display: none;"  @endif>
                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaProductos">Usuario</label>
                    <select required class="form-control-modern" name="cTipoBusquedaVenta" id="cTipoBusquedaVenta">
                        <option value="T">Todos</option>
                        @foreach ($users as $user)
                        <option @if ($user->id != 1 && Auth::user()->id == $user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-bordered" id="gridVentas"></table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('ventas.modals.editNombreVentaModal')

{{-- libs para generación de PDF en cliente (html2canvas + jsPDF UMD) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="{{asset('js/ventas.js')}}"></script>

@endsection
