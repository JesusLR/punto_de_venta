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
@section("titulo", "Realizar venta")
@section("contenido")

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <h1><i class="fa fa-cart-plus"></i> Nueva venta</h1>
        </div>

        @include("notificacion")

        <div class="row">
            <div class="col-12 col-md-6">
                <form action="{{route('terminarOCancelarVenta')}}" method="post" class="form-container">
                    @csrf
                    <input type="hidden" id="userID" name="userID" value="{{ Auth::user()->id }}">

                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Cliente</label>
                        <select required class="form-control-modern" name="id_cliente" id="id_cliente">
                            @foreach($clientes as $cliente)
                                <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(session('productos') !== null)
                        <div class="form-group" style="margin-top:1rem;">
                            <button name="accion" value="terminar" type="submit" class="btn-action btn-success-modern">
                                <i class="fas fa-dollar-sign"></i> Terminar venta
                            </button>
                            <button name="accion" value="apartar" type="submit" class="btn-action btn-secondary">
                                <i class="fas fa-hand-holding-usd"></i> Apartar
                            </button>
                            <button name="accion" value="cancelar" type="submit" class="btn-action" style="background:#FF6B6B; color:#fff;">
                                <i class="far fa-window-close"></i> Cancelar venta
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-container">
                    <div class="form-group">
                        <label><i class="fas fa-box"></i> Producto</label>
                        <select required class="form-control-modern" name="id_producto" id="id_producto">
                            <option value="0"> -- Seleccione un producto -- </option>
                            @foreach($productos as $producto)
                                <option value="{{$producto->id}}">{{$producto->codigo_barras}} - {{$producto->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>

                    <form action="{{route('agregarProductoVenta')}}" method="post" id="formCodigo" style="margin-top:1rem;">
                        @csrf
                        <div class="form-group">
                            <input hidden id="codigo" autocomplete="off" name="codigo" type="text" class="form-control-modern" placeholder="Código de barras" autofocus>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="form-container" style="margin-top:1rem;">
            @if(session('productos') !== null)
                <h3 style="margin-bottom:1rem;">Total: ${{ number_format($total, 2) }}</h3>

                <div style="margin-bottom:1rem;">
                    <button type="button" id="btnPrintTicket" class="btn-action btn-success-modern">
                        <i class="fas fa-print"></i> Imprimir ticket
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código de barras</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Quitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('productos') as $producto)
                                <tr>
                                    <td>{{ $producto->codigo_barras }}</td>
                                    <td>{{ $producto->descripcion }}</td>
                                    <td>${{ number_format($producto->precio_venta, 2) }}</td>
                                    <td>{{ $producto->cantidad }}</td>
                                    <td>
                                        <form action="{{route('quitarProductoDeVenta')}}" method="post">
                                            @method('delete')
                                            @csrf
                                            <input type="hidden" name="indice" value="{{ $loop->index }}">
                                            <button type="submit" class="btn-table-action btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center" style="padding:2rem;">
                    <h4>Aquí aparecerán los productos de la venta</h4>
                    <p>Escanea el código de barras o escribe y presiona Enter</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- pasar datos de la venta a JS --}}
@if(session('productos') !== null)
<script>
    window.ventaData = @json(session('productos'));
    window.ventaTotal = {{ $total }};
    window.ventaFecha = "{{ now()->format('d/m/Y H:i') }}";
    window.ventaUsuario = "{{ Auth::user()->name }}";
</script>
@endif

{{-- libs para generación de PDF en cliente (html2canvas + jsPDF UMD) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="{{ asset('js/vender.js') }}"></script>

@endsection
