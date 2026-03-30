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
@section("titulo", "Detalle de venta")
@section("contenido")

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="productos-header">
            <h1>
                <i class="fas fa-receipt"></i>
                @if (strlen($venta->cNombreVenta) == 0)
                    Detalle de venta #{{$venta->id}}
                @else
                    {{$venta->cNombreVenta}}
                @endif
            </h1>
            <div class="header-actions">
                <a href="{{route("ventas.index")}}" class="btn-modern btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
                <button type="button" id="btnPrintTicketVenta" class="btn-modern btn-success-modern">
                    <i class="fas fa-file-pdf"></i>
                    Ticket (PDF)
                </button>
            </div>
        </div>

        @include("notificacion")

        <!-- Info Cliente -->
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-user"></i> Información de la Venta
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Cliente:</strong> {{$venta->cliente->nombre}}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> {{ $venta->created_at ? $venta->created_at->format('d/m/Y H:i') : '' }}</p>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="table-container">
            <div class="section-title">
                <i class="fas fa-box-open"></i> Productos
            </div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Código de barras</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->productos as $producto)
                                <tr>
                                    <td>{{$producto->descripcion}}</td>
                                    <td>{{$producto->codigo_barras}}</td>
                                    <td>${{number_format($producto->precio, 2)}}</td>
                                    <td>{{$producto->cantidad}}</td>
                                    <td>${{number_format($producto->cantidad * $producto->precio, 2)}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f7f7f7;">
                                <td colspan="4" style="text-align:right;font-weight:700;font-size:1.1rem;">Total</td>
                                <td style="font-weight:700;font-size:1.1rem;">${{number_format($total, 2)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- preparar datos para JS y cargar libs + ventas.js --}}
@php
    $items = $venta->productos->map(function($p){
        return [
            'codigo_barras' => $p->codigo_barras,
            'descripcion'   => $p->descripcion,
            'cantidad'      => $p->cantidad,
            'precio_venta'  => $p->precio
        ];
    });
    $fecha = $venta->created_at ? $venta->created_at->format('d/m/Y H:i') : '';
    $usuario = $venta->user->name ?? '';
@endphp

<script>
    window.saleData = @json($items);
    window.saleTotal = {{ $total }};
    window.saleFecha = "{{ $fecha }}";
    window.saleUsuario = "{{ $usuario }}";
</script>

{{-- libs para generación de PDF en cliente (html2canvas + jsPDF UMD) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="{{ asset('js/ventas.js') }}"></script>

<script>
    // llamar a la función que ya existe en ventas.js para mostrar PDF
    document.getElementById('btnPrintTicketVenta').addEventListener('click', function () {
        if (typeof printTicketVenta === 'function') {
            printTicketVenta(window.saleData, window.saleTotal, window.saleFecha, window.saleUsuario);
        } else {
            alert('Función de impresión no disponible.');
        }
    });
</script>

@endsection
