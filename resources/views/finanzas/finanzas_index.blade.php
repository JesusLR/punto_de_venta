@extends("maestra")
@section("titulo", "Finanzas")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <h1><i class="fas fa-wallet"></i> Ingresos y egresos</h1>
        </div>

        @include("notificacion")
        <div id="finanzas-ajax-alert"></div>

        <div class="filters-container mb-3">
            <form method="GET" action="{{ route('finanzas.index') }}" class="row" id="finanzas-filtros-form">
                <div class="col-md-4 mb-2">
                    <label for="fecha_inicio">Fecha inicio</label>
                    <input type="date" class="form-control-modern" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}" required>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="fecha_fin">Fecha fin</label>
                    <input type="date" class="form-control-modern" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}" required>
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <div class="w-100 d-flex">
                        <button type="submit" class="btn-modern btn-primary-modern w-50 mr-1">
                            <i class="fas fa-search mr-1"></i> Consultar
                        </button>
                        <a href="{{ route('finanzas.excel', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" class="btn-modern btn-success-modern w-50 ml-1" id="btn-exportar-excel">
                            <i class="fas fa-file-excel mr-1"></i> Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div id="finanzas-dashboard">
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Ingresos automáticos</small>
                        <h4 class="mb-0 text-success">${{ number_format($totalIngresos, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Egresos capturados</small>
                        <h4 class="mb-0 text-danger">${{ number_format($totalEgresos, 2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Balance neto</small>
                        <h4 class="mb-0 {{ $balanceNeto >= 0 ? 'text-success' : 'text-danger' }}">${{ number_format($balanceNeto, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="table-container">
                    <div class="table-wrapper">
                        <h5 class="mb-3"><i class="fas fa-minus-circle text-danger"></i> Capturar egreso</h5>

                        <form method="POST" action="{{ route('finanzas.store') }}" id="finanzas-egreso-form">
                            @csrf
                            <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
                            <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">

                            <div class="form-group">
                                <label for="concepto">Concepto</label>
                                <input type="text" class="form-control-modern" id="concepto" name="concepto" maxlength="150" placeholder="..." required>
                            </div>

                            <div class="form-group">
                                <label for="monto">Monto</label>
                                <input type="number" class="form-control-modern" id="monto" name="monto" placeholder="0.00" min="0.01" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="fecha">Fecha</label>
                                <input type="date" class="form-control-modern" id="fecha" name="fecha" value="{{ $fechaHoy }}" required>
                            </div>

                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control-modern" id="observaciones" name="observaciones" rows="3" placeholder="Opcional..."></textarea>
                            </div>

                            <button type="submit" class="btn-modern btn-danger w-100">
                                <i class="fas fa-save mr-1"></i> Guardar egreso
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-3">
                <div id="finanzas-ingresos-section">
                <div class="table-container mb-3">
                    <div class="table-wrapper">
                        <h5 class="mb-3"><i class="fas fa-plus-circle text-success"></i> Ingresos automáticos</h5>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body py-2">
                                        <small class="text-muted d-block mb-1">Total en efectivo</small>
                                        <strong class="text-success">${{ number_format($totalIngresoEfectivo, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body py-2">
                                        <small class="text-muted d-block mb-1">Total en mercado pago</small>
                                        <strong class="text-primary">${{ number_format($totalIngresoMercadoPago, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Referencia</th>
                                        <th>Método</th>
                                        <th>Detalle</th>
                                        <th class="text-right">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($movimientosIngresos as $movimiento)
                                        <tr>
                                            <td>{{ $movimiento['fecha']->format('d/m/Y') }}</td>
                                            <td>{{ $movimiento['tipo'] }}</td>
                                            <td>{{ $movimiento['referencia'] }}</td>
                                            <td>{{ str_replace('_', ' ', $movimiento['metodo']) }}</td>
                                            <td>{{ $movimiento['detalle'] }}</td>
                                            <td class="text-right text-success">${{ number_format($movimiento['monto'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Sin ingresos en el rango seleccionado</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            {{ $movimientosIngresos->links() }}
                        </div>
                    </div>
                </div>
                </div>

                <div id="finanzas-egresos-section">
                <div class="table-container">
                    <div class="table-wrapper">
                        <h5 class="mb-3"><i class="fas fa-list text-danger"></i> Egresos capturados</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Usuario</th>
                                        <th>Observaciones</th>
                                        <th class="text-right">Monto</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($egresos as $egreso)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($egreso->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $egreso->concepto }}</td>
                                            <td>{{ optional($egreso->usuario)->name }}</td>
                                            <td>{{ $egreso->observaciones }}</td>
                                            <td class="text-right text-danger">${{ number_format($egreso->monto, 2) }}</td>
                                            <td class="text-center">
                                                <form method="POST" action="{{ route('finanzas.destroy', $egreso->id) }}" class="finanzas-egreso-delete-form" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
                                                    <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Sin egresos en el rango seleccionado</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            {{ $egresos->links() }}
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        function mostrarAlertaAjax(mensaje, tipo) {
            const clase = tipo === 'success' ? 'alert-success' : 'alert-danger';
            $('#finanzas-ajax-alert').html('<div class="alert ' + clase + '">' + mensaje + '</div>');
        }

        function obtenerUrlConFiltros() {
            return $('#finanzas-filtros-form').attr('action') + '?' + $('#finanzas-filtros-form').serialize();
        }

        function actualizarLinkExcel() {
            const base = '{{ route('finanzas.excel') }}';
            const query = $('#finanzas-filtros-form').serialize();
            $('#btn-exportar-excel').attr('href', base + '?' + query);
        }

        function recargarDashboard(url, callback) {
            const $dashboard = $('#finanzas-dashboard');
            $dashboard.css('opacity', '0.55');

            $.get(url, function (htmlResponse) {
                const $html = $('<div>').html(htmlResponse);
                const $nuevoDashboard = $html.find('#finanzas-dashboard').first();

                if ($nuevoDashboard.length) {
                    $dashboard.replaceWith($nuevoDashboard);
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, '', url);
                    }
                }
            }).always(function () {
                $('#finanzas-dashboard').css('opacity', '1');
                if (typeof callback === 'function') {
                    callback();
                }
            });
        }

        $(document).on('submit', '#finanzas-filtros-form', function (e) {
            e.preventDefault();
            actualizarLinkExcel();
            recargarDashboard(obtenerUrlConFiltros());
        });

        $(document).on('submit', '#finanzas-egreso-form', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $monto = $form.find('#monto');
            $monto.val(String($monto.val()).replace(',', '.'));

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'Accept': 'application/json'
                }
            }).done(function (response) {
                const mensaje = response && response.cMensaje ? response.cMensaje : 'Egreso guardado con éxito';
                actualizarLinkExcel();
                recargarDashboard(obtenerUrlConFiltros(), function () {
                    mostrarAlertaAjax(mensaje, 'success');
                    const $formNuevo = $('#finanzas-egreso-form');
                    $formNuevo.find('#concepto').val('');
                    $formNuevo.find('#monto').val('');
                    $formNuevo.find('#observaciones').val('');
                });
            }).fail(function (xhr) {
                const mensaje = xhr.responseJSON && xhr.responseJSON.cMensaje
                    ? xhr.responseJSON.cMensaje
                    : 'No se pudo guardar el egreso';
                mostrarAlertaAjax(mensaje, 'error');
            });
        });

        $(document).on('submit', '.finanzas-egreso-delete-form', function (e) {
            e.preventDefault();

            if (!confirm('¿Eliminar este egreso?')) {
                return;
            }

            const $form = $(this);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'Accept': 'application/json'
                }
            }).done(function (response) {
                const mensaje = response && response.cMensaje ? response.cMensaje : 'Egreso eliminado con éxito';
                actualizarLinkExcel();
                recargarDashboard(obtenerUrlConFiltros(), function () {
                    mostrarAlertaAjax(mensaje, 'success');
                });
            }).fail(function () {
                mostrarAlertaAjax('No se pudo eliminar el egreso', 'error');
            });
        });

        $(document).on('click', '#finanzas-ingresos-section .pagination a, #finanzas-egresos-section .pagination a', function (e) {
            e.preventDefault();

            const url = $(this).attr('href');
            const esIngresos = $(this).closest('#finanzas-ingresos-section').length > 0;
            const targetSelector = esIngresos ? '#finanzas-ingresos-section' : '#finanzas-egresos-section';

            const $target = $(targetSelector);
            $target.css('opacity', '0.55');

            $.get(url, function (htmlResponse) {
                const $html = $('<div>').html(htmlResponse);
                const $nuevoBloque = $html.find(targetSelector).first();

                if ($nuevoBloque.length) {
                    $target.replaceWith($nuevoBloque);
                    actualizarLinkExcel();
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, '', url);
                    }
                }
            }).always(function () {
                $(targetSelector).css('opacity', '1');
            });
        });

        actualizarLinkExcel();
    });
</script>
@endsection
