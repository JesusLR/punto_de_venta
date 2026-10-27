@extends("maestra")
@section("titulo", "Configuración General")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<style>
    .setting-card {
        background: white;
        border-radius: 12px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
        margin-top: 1.5rem;
    }
    .section-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 1.15rem;
        margin-bottom: 0.5rem;
    }
    .form-group-modern label {
        font-weight: 700;
        color: #334155;
        font-size: 0.88rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-group-modern label i {
        color: #D4AF37;
        margin-right: 4px;
    }
    .form-control-modern {
        border-radius: 8px;
        border: 1.5px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        background-color: #fff;
        width: 100%;
        color: #1e293b;
    }
    .form-control-modern:focus {
        border-color: #D4AF37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
        outline: none;
    }
    .form-text {
        font-size: 0.8rem;
        margin-top: 0.4rem;
        color: #64748b;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="productos-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1><i class="fas fa-cogs"></i> Configuración General</h1>
                <p style="margin-top: 5px; color: rgba(255,255,255,0.8);">Establece los parámetros y configuraciones generales del funcionamiento de tu punto de venta.</p>
            </div>
            <div class="mt-2 mt-md-0">
                <button type="button" onclick="document.getElementById('formGeneralSettings').submit();" class="btn btn-action" style="background:#D4AF37; color:#111; border:none; border-radius: 20px; font-weight: 700; padding: 8px 24px;">
                    <i class="fas fa-save mr-1"></i> Guardar Ajustes
                </button>
            </div>
        </div>

        @if(session('mensaje'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('mensaje') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 8px;">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="formGeneralSettings" action="{{ route('general.settings.update') }}" method="POST">
            @csrf

            <div class="setting-card">
                <h4 class="section-title"><i class="fas fa-bell mr-2" style="color:#D4AF37;"></i> Alertas y Recordatorios de Apartados</h4>
                <p class="text-muted small mb-4">Configura los recordatorios de inactividad que se calculan para alertar al administrador sobre apartados sin movimiento.</p>
                
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group-modern mb-3">
                            <label for="apartado_inactive_days"><i class="fas fa-calendar-alt"></i> Días de inactividad permitidos</label>
                            <input type="number" name="apartado_inactive_days" id="apartado_inactive_days" class="form-control-modern" value="{{ $settings['apartado_inactive_days'] ?? '30' }}" min="1" max="365" required>
                            <span class="form-text">Especifica cuántos días de inactividad (sin abonos o modificaciones) deben transcurrir para activar la notificación.</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="setting-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div>
                    <h4 class="section-title"><i class="fas fa-history mr-2" style="color:#D4AF37;"></i> Egresos Automáticos Programados</h4>
                    <p class="text-muted small mb-0">Configura egresos fijos que se registrarán de forma automática con frecuencia mensual o semanal.</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <button type="button" class="btn btn-action" data-toggle="modal" data-target="#modalAgregarEgresoAuto" style="background:#10b981; color:white; border:none; border-radius: 20px; font-weight: 700; padding: 8px 20px;">
                        <i class="fas fa-plus mr-1"></i> Programar Egreso
                    </button>
                </div>
            </div>

            <div id="general-alert-container"></div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" id="tabla-egresos-auto">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th class="text-right">Monto</th>
                            <th class="text-center">Frecuencia / Ejecución</th>
                            <th>Observaciones</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($egresosAutomaticos as $egresoAuto)
                            <tr>
                                <td style="vertical-align: middle; font-weight: 700;">{{ $egresoAuto->concepto }}</td>
                                <td class="text-right text-danger" style="vertical-align: middle; font-weight: 700;">${{ number_format($egresoAuto->monto, 2) }}</td>
                                <td class="text-center" style="vertical-align: middle;">
                                    @if ($egresoAuto->frecuencia === 'MENSUAL')
                                        <span class="badge badge-info px-2 py-1" style="font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> Mensual (Día {{ $egresoAuto->dia_mes }})</span>
                                    @else
                                        @php
                                            $diasSemana = [
                                                1 => 'Lunes',
                                                2 => 'Martes',
                                                3 => 'Miércoles',
                                                4 => 'Jueves',
                                                5 => 'Viernes',
                                                6 => 'Sábado',
                                                7 => 'Domingo'
                                            ];
                                            $diaSemanaNombre = $diasSemana[$egresoAuto->dia_semana] ?? 'N/A';
                                        @endphp
                                        <span class="badge badge-success px-2 py-1" style="font-size: 0.85rem;"><i class="far fa-clock"></i> Semanal ({{ $diaSemanaNombre }})</span>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">{{ $egresoAuto->observaciones ?: '-' }}</td>
                                <td class="text-center" style="vertical-align: middle;">
                                    <form method="POST" action="{{ route('general.egresos_automaticos.destroy', $egresoAuto->id) }}" class="form-eliminar-egreso-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar programación">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No hay egresos automáticos programados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Egreso Automático -->
<div class="modal fade" id="modalAgregarEgresoAuto" tabindex="-1" role="dialog" aria-labelledby="modalAgregarEgresoAutoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-bottom: 2px solid #D4AF37;">
                <h5 class="modal-title" id="modalAgregarEgresoAutoLabel" style="font-weight: 800;"><i class="fas fa-calendar-plus mr-2" style="color: #D4AF37;"></i> Programar Egreso Automático</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-nuevo-egreso-auto" action="{{ route('general.egresos_automaticos.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="modal-alert-container"></div>
                    
                    <div class="form-group mb-3">
                        <label for="auto_concepto" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Concepto</label>
                        <input type="text" class="form-control-modern" id="auto_concepto" name="concepto" placeholder="Ej. RENTA LOCAL, INTERNET, LUZ" maxlength="150" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="auto_monto" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Monto ($)</label>
                                <input type="number" class="form-control-modern" id="auto_monto" name="monto" placeholder="0.00" min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-3">
                                <label for="auto_frecuencia" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Frecuencia</label>
                                <select class="form-control-modern" id="auto_frecuencia" name="frecuencia" required>
                                    <option value="MENSUAL">Mensual</option>
                                    <option value="SEMANAL">Semanal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12" id="grupo_dia_mes">
                            <div class="form-group mb-3">
                                <label for="auto_dia_mes" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Día de Ejecución (Mes)</label>
                                <select class="form-control-modern" id="auto_dia_mes" name="dia_mes" required>
                                    @for ($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}">Día {{ $i }} del mes</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-12" id="grupo_dia_semana" style="display: none;">
                            <div class="form-group mb-3">
                                <label for="auto_dia_semana" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Día de Ejecución (Semana)</label>
                                <select class="form-control-modern" id="auto_dia_semana" name="dia_semana">
                                    <option value="1">Lunes</option>
                                    <option value="2">Martes</option>
                                    <option value="3">Miércoles</option>
                                    <option value="4">Jueves</option>
                                    <option value="5">Viernes</option>
                                    <option value="6">Sábado</option>
                                    <option value="7">Domingo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="auto_observaciones" style="font-weight: 700; color: #334155; font-size: 0.88rem;">Observaciones</label>
                        <textarea class="form-control-modern" id="auto_observaciones" name="observaciones" rows="3" placeholder="Opcional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal" style="border-radius: 20px; font-weight: 700;">Cancelar</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 20px; font-weight: 700;"><i class="fas fa-save mr-1"></i> Programar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        function recargarTablaEgresosAuto() {
            const url = '{{ route("general.settings.index") }}';
            $.get(url, function (htmlResponse) {
                const $html = $('<div>').html(htmlResponse);
                const $nuevaTabla = $html.find('#tabla-egresos-auto').first();
                if ($nuevaTabla.length) {
                    $('#tabla-egresos-auto').replaceWith($nuevaTabla);
                }
            });
        }

        $(document).on('change', '#auto_frecuencia', function () {
            const freq = $(this).val();
            if (freq === 'MENSUAL') {
                $('#grupo_dia_mes').show();
                $('#auto_dia_mes').prop('required', true);
                $('#grupo_dia_semana').hide();
                $('#auto_dia_semana').prop('required', false);
            } else {
                $('#grupo_dia_mes').hide();
                $('#auto_dia_mes').prop('required', false);
                $('#grupo_dia_semana').show();
                $('#auto_dia_semana').prop('required', true);
            }
        });

        $(document).on('submit', '#form-nuevo-egreso-auto', function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btnSubmit = $form.find('button[type="submit"]');
            $btnSubmit.prop('disabled', true);

            const $monto = $form.find('#auto_monto');
            $monto.val(String($monto.val()).replace(',', '.'));

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'Accept': 'application/json'
                }
            }).done(function (response) {
                $('#modalAgregarEgresoAuto').modal('hide');
                $form.find('input[type="text"], input[type="number"], textarea').val('');
                $form.find('select').val('1');
                $('#auto_frecuencia').val('MENSUAL').trigger('change');
                $('#modal-alert-container').html('');
                
                const msg = response && response.cMensaje ? response.cMensaje : 'Egreso automático programado correctamente.';
                Swal.fire({
                    icon: 'success',
                    title: '¡Programado!',
                    text: msg,
                    confirmButtonColor: '#D4AF37',
                    timer: 3000
                });
                
                recargarTablaEgresosAuto();
            }).fail(function (xhr) {
                const err = xhr.responseJSON && xhr.responseJSON.cMensaje ? xhr.responseJSON.cMensaje : 'Error al programar el egreso automático.';
                $('#modal-alert-container').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>' + err + '</div>');
            }).always(function () {
                $btnSubmit.prop('disabled', false);
            });
        });

        $(document).on('submit', '.form-eliminar-egreso-auto', function (e) {
            e.preventDefault();
            const $form = $(this);

            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Estás seguro de que deseas eliminar este egreso automático? Ya no se generará en los meses o semanas siguientes.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const $btnSubmit = $form.find('button[type="submit"]');
                    $btnSubmit.prop('disabled', true);

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        headers: {
                            'Accept': 'application/json'
                        }
                    }).done(function (response) {
                        const msg = response && response.cMensaje ? response.cMensaje : 'Egreso automático eliminado correctamente.';
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: msg,
                            confirmButtonColor: '#D4AF37',
                            timer: 3000
                        });
                        recargarTablaEgresosAuto();
                    }).fail(function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el egreso automático.',
                            confirmButtonColor: '#D4AF37'
                        });
                        $btnSubmit.prop('disabled', false);
                    });
                }
            });
        });

        @if(session('mensaje'))
            Swal.fire({
                icon: 'success',
                title: '¡Guardado!',
                text: "{{ session('mensaje') }}",
                confirmButtonColor: '#D4AF37',
                timer: 3000
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#D4AF37'
            });
        @endif
    });
</script>
@endsection
