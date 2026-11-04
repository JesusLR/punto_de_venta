@extends('maestra')
@section('titulo', 'Inicio de sesión de WhatsApp')
@section('contenido')
<div class="container-fluid px-0 px-md-3" id="whatsappLogin"
     data-list="{{ route('whatsapp.settings.sessions.list') }}"
     data-create="{{ route('whatsapp.settings.sessions.create') }}"
     data-start="{{ route('whatsapp.settings.sessions.start') }}"
     data-stop="{{ route('whatsapp.settings.sessions.stop') }}"
     data-active="{{ route('whatsapp.settings.sessions.active') }}"
     data-qr="{{ url('/whatsapp/settings/sessions') }}">
    <div class="mb-4 p-4 rounded-lg shadow-sm" style="background:linear-gradient(135deg,#0f172a,#1e293b);border-left:5px solid #25D366">
        <h2 class="h3 font-weight-bold text-white mb-2"><i class="fab fa-whatsapp text-success mr-2"></i> Inicio de sesión</h2>
        <p class="mb-0" style="color:#cbd5e1">Vincula WhatsApp para enviar los PDF de ventas y apartados.</p>
    </div>
    <div id="ajaxAlertContainer" role="status" aria-live="polite"></div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-qrcode text-success mr-2"></i> Vincular WhatsApp</span>
                    <button type="button" class="btn btn-sm btn-light border" id="btnRefreshSession"><i class="fas fa-sync-alt"></i> Actualizar</button>
                </div>
                <div class="card-body">
                    <label for="sessionSelect" class="font-weight-bold">Sesión de WhatsApp</label>
                    <div class="input-group mb-2">
                        <select id="sessionSelect" class="form-control"><option value="">Cargando sesiones…</option></select>
                        <div class="input-group-append">
                            <button type="button" id="btnSetActiveSession" class="btn btn-outline-primary" disabled>Usar para envíos</button>
                            <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#modalCreateSession">Nueva sesión</button>
                        </div>
                    </div>
                    <p class="small text-muted mb-4" id="activeSessionBadge">Solo se muestran las sesiones de este sistema.</p>
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center mb-4">
                            <div class="bg-light rounded border p-3 d-flex align-items-center justify-content-center" style="min-height:260px">
                                <img id="qrImage" alt="Código QR para vincular WhatsApp" style="max-width:230px;width:100%" hidden>
                                <div id="qrMessage" class="text-muted">Selecciona o crea una sesión para comenzar.</div>
                            </div>
                            <span id="statusBadge" class="badge badge-secondary mt-3 px-3 py-2">SIN SESIÓN</span>
                        </div>
                        <div class="col-md-6">
                            <dl class="mb-4">
                                <dt>Teléfono vinculado</dt><dd id="infoPhone">—</dd>
                                <dt>Nombre de WhatsApp</dt><dd id="infoPushName">—</dd>
                            </dl>
                            <button type="button" class="btn btn-success btn-block mb-2" id="btnStartSession" disabled><i class="fas fa-play mr-1"></i> Iniciar sesión / Ver QR</button>
                            <button type="button" class="btn btn-outline-danger btn-block" id="btnStopSession" disabled><i class="fas fa-sign-out-alt mr-1"></i> Cerrar sesión</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Conecta tu número</h5>
                    <ol class="pl-3 text-muted">
                        <li class="mb-2">Crea una sesión o selecciona una existente.</li>
                        <li class="mb-2">Pulsa Iniciar sesión para obtener el código QR.</li>
                        <li class="mb-2">En WhatsApp abre Dispositivos vinculados y escanea el código.</li>
                        <li>Pulsa Usar para envíos para elegir el número que enviará los documentos.</li>
                    </ol>
                    <p class="small text-muted mb-0">Cuando el estado sea Conectado podrás enviar los PDF desde ventas y apartados.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCreateSession" tabindex="-1" role="dialog" aria-labelledby="createSessionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content" id="createSessionForm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createSessionTitle">Nueva sesión de WhatsApp</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <label for="newSessionName">Nombre de la sesión</label>
                <input id="newSessionName" class="form-control" placeholder="Ejemplo: sucursal-centro" minlength="3" maxlength="50" pattern="[a-zA-Z0-9-]+" required>
                <small class="form-text text-muted">De 3 a 50 caracteres. Usa letras sin acentos, números y guiones.</small>
                <p id="createSessionError" class="text-danger small mt-2" role="alert"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btnSubmitCreateSession">Crear e iniciar</button>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/whatsapp-login.js') }}?v={{ filemtime(public_path('js/whatsapp-login.js')) }}"></script>
@endsection
