@extends('maestra')
@section('titulo', 'Reparaciones')
@section('contenido')
<div id="reparacionesApp" data-productos="{{ route('reparaciones.productos') }}" data-guardar="{{ route('reparaciones.store') }}">
    <div class="productos-header">
        <div class="d-flex align-items-center" style="gap:18px">
            <span class="workspace-header-icon"><i class="fas fa-tools"></i></span>
            <div>
            <span class="workspace-eyebrow">Tienda · Reparaciones</span>
            <h1>Reparaciones</h1>
            <p class="workspace-description">Control de piezas del inventario y reparaciones externas, con su seguimiento y movimientos de Finanzas.</p>
            </div>
        </div>
        @if(Auth::user()->hasPermission('manage_reparaciones'))
            <div class="header-actions"><button type="button" class="btn-modern btn-success-modern" data-toggle="modal" data-target="#nuevaReparacionModal"><i class="fas fa-plus"></i> Interna</button><button type="button" class="btn-modern btn-primary-modern" id="btnNuevaReparacionExterna"><i class="fas fa-user-plus"></i> Externa</button></div>
        @endif
    </div>
    <form id="filtrosReparaciones" method="GET" class="filters-container" action="{{ route('reparaciones.index') }}">
        <h2 class="workspace-section-heading"><i class="fas fa-filter"></i> Filtrar reparaciones</h2>
        <div class="row align-items-end">
            <div class="col-md-5 form-group-modern mb-0">
                <label for="estadoReparacion">Estado</label>
                <select name="estado" id="estadoReparacion" class="form-control">
                    <option value="">Todos</option>
                    <option value="EN_REPARACION" @if($estadoFiltro === 'EN_REPARACION') selected @endif>En reparación</option>
                    <option value="ENTREGADO" @if($estadoFiltro === 'ENTREGADO') selected @endif>Entregado</option>
                    <option value="CANCELADO" @if($estadoFiltro === 'CANCELADO') selected @endif>Cancelado</option>
                </select>
            </div>
            <div class="col-md-5 form-group-modern mb-0">
                <label for="usuarioReparacion">Registrado por</label>
                @if($puedeVerTodas)
                    <select name="usuario_id" id="usuarioReparacion" class="form-control">
                        <option value="">Todos los usuarios</option>
                        @foreach($usuariosFiltro as $usuario)
                            <option value="{{ $usuario->id }}" @if((string) $usuarioFiltro === (string) $usuario->id) selected @endif>{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input id="usuarioReparacion" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    <small class="form-text text-muted">Solo se muestran las reparaciones que registraste.</small>
                @endif
            </div>
        </div>
    </form>
    <div id="reparacionesGridError" class="alert alert-danger d-none" role="alert"></div>
    <div id="reparacionesResultados" aria-live="polite" aria-busy="false">@include('reparaciones.grid')</div>
</div>

@include('reparaciones.modals.visores')

@if(Auth::user()->hasPermission('manage_reparaciones'))
@include('reparaciones.modals.nombre')
@include('reparaciones.modals.externa')
<div class="modal fade apartado-modal" id="nuevaReparacionModal" tabindex="-1" role="dialog" aria-labelledby="nuevaReparacionTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
        <form class="modal-content" id="formReparacion">
            @csrf
            <input type="hidden" name="solicitud_id" value="{{ \Illuminate\Support\Str::uuid() }}">
            <div class="modal-header">
                <div><span class="apartado-modal-badge"><i class="fas fa-tools"></i> Interna</span><h5 class="modal-title" id="nuevaReparacionTitulo">Nueva reparación</h5><p class="apartado-modal-subtitle mb-0">Las unidades seleccionadas dejarán de estar disponibles para venta hasta recibirlas.</p></div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="reparacionErrores" role="alert" aria-live="polite"></div>
                <div class="form-group-modern"><label for="nombreNuevaReparacion">Nombre de la reparación (opcional)</label><input name="nombre" id="nombreNuevaReparacion" class="form-control" maxlength="150" placeholder="Por ejemplo: Soldadura de anillos"></div>
                <div class="reparacion-piezas-layout">
                <section class="reparacion-form-panel" aria-labelledby="reparacionBuscarTitulo">
                <h6 class="workspace-section-heading" id="reparacionBuscarTitulo"><i class="fas fa-search"></i> Seleccionar productos</h6>
                <div class="form-group-modern"><label for="buscarPiezaReparacion">Código o descripción</label><input id="buscarPiezaReparacion" class="form-control" maxlength="255" placeholder="Busca una pieza del inventario" autocomplete="off"></div>
                <div id="resultadosPiezasReparacion" class="list-group" aria-live="polite"></div>
                </section>
                <section class="reparacion-form-panel" aria-labelledby="reparacionSeleccionTitulo">
                <h6 class="workspace-section-heading" id="reparacionSeleccionTitulo"><i class="fas fa-gem"></i> Piezas a reparar</h6>
                <div class="table-responsive"><table class="table"><thead><tr><th>Producto</th><th>Disponibles</th><th>Unidades a reparar</th><th></th></tr></thead><tbody id="piezasReparacionSeleccionadas"></tbody></table></div>
                <p id="reparacionSinPiezas" class="text-muted">Agrega al menos una pieza desde el buscador.</p>
                </section>
                </div>
                <div class="reparacion-form-panel mt-3">
                <h6 class="workspace-section-heading"><i class="fas fa-receipt"></i> Costo y detalles</h6>
                <div class="row">
                    <div class="col-md-4 form-group-modern mb-0"><label for="totalReparacion">Precio total de la reparación</label><input type="number" name="total" id="totalReparacion" class="form-control" step="0.01" min="0" max="9999999999.99" required placeholder="0.00"><small class="form-text text-muted">Este importe se registra como egreso al marcar entregado.</small></div>
                    <div class="col-md-8 form-group-modern mb-0"><label for="observacionesReparacion">Observaciones</label><textarea name="observaciones" id="observacionesReparacion" class="form-control" rows="2" maxlength="3000" placeholder="Trabajo a realizar o detalles de la reparación"></textarea></div>
                </div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn apartado-modal-btn-cancel" data-dismiss="modal">Cancelar</button><button type="submit" class="btn-modern btn-success-modern" id="guardarReparacion"><i class="fas fa-save"></i> Registrar reparación</button></div>
        </form>
    </div>
</div>
@endif
<script src="{{ asset('js/reparaciones.js') }}?v={{ filemtime(public_path('js/reparaciones.js')) }}"></script>
<script src="{{ asset('js/reparaciones-visores.js') }}?v={{ filemtime(public_path('js/reparaciones-visores.js')) }}"></script>
@if(Auth::user()->hasPermission('manage_reparaciones'))<script src="{{ asset('js/reparaciones-externas.js') }}?v={{ filemtime(public_path('js/reparaciones-externas.js')) }}"></script>@endif
@endsection
