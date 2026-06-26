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
    </div>
</div>

@endsection
