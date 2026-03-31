@extends("maestra")
@section("titulo", "Gestión de Apartados")
@section("contenido")

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<input type="text" id="userID" value="{{Auth::user()->id}}" hidden>
<input type="text" id="iIDApartado" hidden>

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <h1><i class="fas fa-clipboard-list"></i> Gestión de Apartados</h1>
        </div>

        @include("notificacion")

        <div class="filters-container">
            <div class="row" @if (Auth::user()->id != 1) style="display: none;"  @endif>
                <div class="col-md-6 mb-3">
                    <label for="cTipoBusquedaApartado">Usuario</label>
                    <select required class="form-control-modern" name="cTipoBusquedaApartado" id="cTipoBusquedaApartado">
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
                    <table class="table table-bordered" id="gridApartados"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Abono -->
<div class="modal" id="modalAbono" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill"></i> Nuevo Abono</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white; font-size: 1.2em;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formAbono" method="post">
                @csrf
                <div class="modal-body" style="padding: 12px;">
                    <input type="hidden" id="id_apartado_abono" name="id_apartado">

                    <!-- Formulario compacto -->
                    <div class="form-group ">
                        <label for="monto_abono">Monto</label>
                        <input type="number" id="monto_abono" class="form-control form-control-sm" name="monto_abono" min="0.01" step="0.01" placeholder="0.00" required >
                        {{-- <small class="form-text text-muted" id="max-abono" style="font-size: 0.7em;"></small> --}}
                    {{-- </div> --}}

                    {{-- <div class="form-group "> --}}
                        <label for="tipo_pago_abono">Tipo de pago</label>
                        <select id="tipo_pago_abono" class="form-control form-control-sm" name="tipo_pago" required>
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="MERCADO_PAGO">Mercado Pago</option>
                        </select>

                        <label for="observaciones_abono">Notas</label>
                        <textarea id="observaciones_abono" class="form-control form-control-sm" name="observaciones" rows="1" placeholder="Opcional..." ></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 8px; border-top: 1px solid #ecf0f1;">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-success" style="background-color: #27ae60; border: none;">
                        <i class="fas fa-save"></i> Abonar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Productos -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-box"></i> Productos del Apartado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="productosBody">
                <!-- Se cargará con AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historial de Abonos -->
<div class="modal fade" id="modalHistorialAbonos" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history"></i> Historial de Abonos</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="historialAbonosBody">
                <!-- Se cargará con AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/apartados.js') }}"></script>

@endsection
