@extends("maestra")
@section("titulo", "Configuración y Sesión de WhatsApp")
@section("contenido")

<div class="container-fluid px-0 px-md-3">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 p-4 rounded-lg shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 5px solid #25D366;">
        <div>
            <h2 class="h3 font-weight-bold text-white mb-1">
                <i class="fab fa-whatsapp text-success mr-2"></i> Gestión de Sesión y Bot de WhatsApp
            </h2>
            <p class="text-muted mb-0 small" style="color: #94a3b8 !important;">
                Inicia sesión, escanea el código QR, reconecta tu número y configura los parámetros de respuesta automática.
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('whatsapp.chat.index') }}" class="btn btn-success btn-sm font-weight-bold mr-2 shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-comments mr-1"></i> Ir al Chat en Vivo
            </a>
            <a href="{{ route('whatsapp.links.index') }}" class="btn btn-outline-light btn-sm shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-link mr-1" style="color: #25D366;"></i> Generador de Enlaces
            </a>
        </div>
    </div>

    @if(session("mensaje"))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{session("mensaje")}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session("error"))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{session("error")}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Alert de Notificación Dinámica AJAX -->
    <div id="ajaxAlertContainer"></div>

    <div class="row">
        <!-- Columna Izquierda: Control de Sesión y Código QR -->
        <div class="col-lg-7 col-xl-8">
            <!-- Card de Estado de Sesión Activa -->
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-qrcode text-success mr-2"></i> Vinculación de Dispositivo / Código QR</span>
                    <button class="btn btn-sm btn-light border" id="btnRefreshSession">
                        <i class="fas fa-sync-alt text-secondary"></i> Refrescar Estado
                    </button>
                </div>
                <div class="card-body">
                    <!-- Selector y Creación de Sesiones -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark d-flex justify-content-between">
                            <span>Seleccionar Sesión de WhatsApp:</span>
                            <span class="badge badge-primary font-weight-normal px-2 py-1" id="activeSessionBadge">
                                Activa actual: {{ $settings['wa_session_id'] ?? 'N/A' }}
                            </span>
                        </label>
                        <div class="input-group">
                            <select id="sessionSelect" class="form-control font-weight-bold">
                                <option value="">Cargando sesiones desde OpenWA...</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="btnSetActiveSession" title="Establecer como la sesión activa del sistema">
                                    <i class="fas fa-star mr-1"></i> Usar como Activa
                                </button>
                                <button class="btn btn-outline-success" type="button" data-toggle="modal" data-target="#modalCreateSession">
                                    <i class="fas fa-plus mr-1"></i> Nueva Sesión
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Puedes tener múltiples sesiones creadas en OpenWA y vincular el número que desees.</small>
                    </div>

                    <hr class="my-4">

                    <!-- Panel Central de Estado y QR -->
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center mb-4 mb-md-0 border-right">
                            <div id="qrContainer" class="p-3 bg-light rounded border d-inline-block shadow-sm" style="min-width: 240px; min-height: 240px; position: relative;">
                                <div id="qrSpinner" class="flex-column justify-content-center align-items-center py-5" style="display: flex;">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <span id="qrSpinnerText" class="text-muted small px-2 text-center mt-2">Consultando código QR...</span>
                                </div>
                                <img id="qrImage" src="" alt="Código QR WhatsApp" class="img-fluid rounded" style="max-width: 220px; height: auto; display: none;">
                                
                                <div id="qrConnectedState" class="py-4" style="display: none;">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-check-circle fa-4x"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">¡WhatsApp Conectado!</h5>
                                    <p class="text-muted small mb-0" id="connectedPhoneText">Número listo para enviar y recibir mensajes.</p>
                                </div>

                                <div id="qrDisconnectedState" class="py-4" style="display: none;">
                                    <div class="text-secondary mb-2">
                                        <i class="fas fa-power-off fa-4x text-muted"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-1">Sesión Desconectada</h5>
                                    <p class="text-muted small mb-3">Haz clic en "Iniciar Sesión" para generar el QR.</p>
                                </div>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="badge badge-pill badge-secondary px-3 py-2" id="statusBadge" style="font-size: 0.9rem;">
                                    VERIFICANDO ESTADO...
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="font-weight-bold text-dark mb-3">Detalles de la Sesión</h5>
                            
                            <ul class="list-group list-group-flush mb-4 small">
                                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                    <span class="text-muted">ID de Sesión:</span>
                                    <strong id="infoSessionId" class="text-monospace">-</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                    <span class="text-muted">Teléfono Vinculado:</span>
                                    <strong id="infoPhone" class="text-success">-</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                    <span class="text-muted">Nombre WhatsApp:</span>
                                    <strong id="infoPushName">-</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                                    <span class="text-muted">Estado en OpenWA:</span>
                                    <strong id="infoStatus" class="text-uppercase">-</strong>
                                </li>
                            </ul>

                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-success font-weight-bold mb-2 py-2" id="btnStartSession" style="border-radius: 8px;">
                                    <i class="fas fa-play mr-1"></i> Iniciar Sesión / Pedir QR
                                </button>
                                <button class="btn btn-outline-danger font-weight-bold py-2" id="btnStopSession" style="border-radius: 8px;">
                                    <i class="fas fa-stop mr-1"></i> Detener / Desconectar Sesión
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Parámetros del Bot -->
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="fas fa-sliders-h text-primary mr-2"></i> Parámetros de Respuestas Automáticas
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.settings.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Estado del Chatbot Automático</label>
                            <select name="wa_bot_enabled" class="form-control">
                                <option value="1" {{ ($settings['wa_bot_enabled'] ?? '1') == '1' ? 'selected' : '' }}>🟢 Habilitado (Responde automáticamente mensajes entrantes)</option>
                                <option value="0" {{ ($settings['wa_bot_enabled'] ?? '1') == '0' ? 'selected' : '' }}>🔴 Deshabilitado (No enviar respuestas automáticas)</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Mensaje de Saludo Personalizado (Opcional)</label>
                            <textarea name="wa_welcome_text" class="form-control" rows="3" placeholder="👋 ¡Hola! Bienvenido a nuestra tienda. (Si se deja vacío, se usará el saludo estándar)">{{ $settings['wa_welcome_text'] }}</textarea>
                            <small class="form-text text-muted">Este mensaje encabeza el menú interactivo enviado a clientes nuevos.</small>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                                <i class="fas fa-save mr-1"></i> Guardar Ajustes del Bot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Información de Webhook -->
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="fas fa-network-wired text-info mr-2"></i> Integración Webhook Entrante
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2"><strong>URL de Webhook Entrante (OpenWA -> POS):</strong></p>
                    <div class="p-2 bg-light rounded text-monospace small mb-3 border text-break">
                        {{ url('/api/whatsapp/webhook') }}
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Asegúrate de que OpenWA envíe sus notificaciones HTTP POST a la URL de webhook anterior para recibir los mensajes entrantes en el sistema.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear Nueva Sesión -->
<div class="modal fade" id="modalCreateSession" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> Crear Nueva Sesión de WhatsApp</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark">Nombre / Identificador de la Sesión:</label>
                    <input type="text" id="newSessionName" class="form-control" placeholder="Ej: Sucursal Centro / Joyería Colibrí 2">
                    <small class="form-text text-muted">Nombre descriptivo para identificar esta línea de WhatsApp en OpenWA.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnSubmitCreateSession">
                    <i class="fas fa-check mr-1"></i> Crear e Iniciar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSessionId = "{{ $settings['wa_session_id'] ?? '' }}";
    let qrPollInterval = null;

    const routes = {
        listSessions: "{{ route('whatsapp.settings.sessions.list') }}",
        createSession: "{{ route('whatsapp.settings.sessions.create') }}",
        startSession: "{{ route('whatsapp.settings.sessions.start') }}",
        stopSession: "{{ route('whatsapp.settings.sessions.stop') }}",
        setActive: "{{ route('whatsapp.settings.sessions.active') }}",
        getQrPrefix: "{{ url('/whatsapp/settings/sessions') }}"
    };

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-1"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        document.getElementById('ajaxAlertContainer').innerHTML = alertHtml;
    }

    // 1. Cargar lista de sesiones desde OpenWA
    function loadSessions() {
        fetch(routes.listSessions)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('sessionSelect');
                select.innerHTML = '';

                if (!data.success || !data.sessions || data.sessions.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = currentSessionId || 'default';
                    opt.textContent = `Sesión actual (${currentSessionId || 'Sin registrar'})`;
                    select.appendChild(opt);
                    showAlert('warning', data.message || 'No se encontraron sesiones en OpenWA. Puedes crear una nueva.');
                } else {
                    data.sessions.forEach(sess => {
                        const opt = document.createElement('option');
                        opt.value = sess.id;
                        const sessStatus = (sess.status || '').toString().toUpperCase();
                        const statusIcon = sessStatus === 'READY' ? '🟢' : (sessStatus === 'QR_READY' ? '🟡' : '🔴');
                        opt.textContent = `${statusIcon} ${sess.name} (${sess.phone || 'Sin número'}) - Status: ${sessStatus}`;
                        if (sess.id === currentSessionId) {
                            opt.selected = true;
                        }
                        select.appendChild(opt);
                    });
                }

                if (select.value) {
                    currentSessionId = select.value;
                    checkQrAndStatus();
                }
            })
            .catch(err => {
                showAlert('danger', 'Error conectando con OpenWA. Verifica que el servidor de WhatsApp esté activo.');
            });
    }

    function showQrSection(section) {
        const spinner = document.getElementById('qrSpinner');
        const qrImg = document.getElementById('qrImage');
        const connectedState = document.getElementById('qrConnectedState');
        const disconnectedState = document.getElementById('qrDisconnectedState');

        spinner.style.display = 'none';
        qrImg.style.display = 'none';
        connectedState.style.display = 'none';
        disconnectedState.style.display = 'none';

        if (section === 'spinner') {
            spinner.style.display = 'flex';
        } else if (section === 'image') {
            qrImg.style.display = 'inline-block';
        } else if (section === 'connected') {
            connectedState.style.display = 'block';
        } else if (section === 'disconnected') {
            disconnectedState.style.display = 'block';
        }
    }

    // 2. Consultar código QR y estado de la sesión seleccionada
    function checkQrAndStatus() {
        if (!currentSessionId) return;

        document.getElementById('infoSessionId').textContent = currentSessionId;

        fetch(`${routes.getQrPrefix}/${currentSessionId}/qr`)
            .then(res => res.json())
            .then(data => {
                const statusBadge = document.getElementById('statusBadge');
                const infoStatus = document.getElementById('infoStatus');
                const infoPhone = document.getElementById('infoPhone');
                const infoPushName = document.getElementById('infoPushName');

                const status = (data.status || 'DISCONNECTED').toString().toUpperCase();
                const phoneVal = data.phone || (data.session && data.session.phone ? data.session.phone : null);
                const pushNameVal = data.pushName || (data.session && data.session.pushName ? data.session.pushName : 'N/A');

                infoStatus.textContent = status;
                infoPhone.textContent = phoneVal || 'No vinculado';
                infoPushName.textContent = pushNameVal;

                const qrImg = document.getElementById('qrImage');

                if (status === 'READY') {
                    statusBadge.className = 'badge badge-pill badge-success px-3 py-2';
                    statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> CONECTADO (READY)';
                    showQrSection('connected');
                    document.getElementById('connectedPhoneText').textContent = phoneVal 
                        ? `Número ${phoneVal} vinculado activamente.` 
                        : 'Sesión vinculada a WhatsApp y lista para enviar y recibir mensajes.';
                    stopQrPolling();

                } else if (status === 'QR_READY') {
                    statusBadge.className = 'badge badge-pill badge-warning px-3 py-2 text-dark';
                    statusBadge.innerHTML = '<i class="fas fa-qrcode mr-1"></i> ESCANEAR CÓDIGO QR';

                    if (data.qrCode) {
                        qrImg.src = data.qrCode.startsWith('data:image') ? data.qrCode : `data:image/png;base64,${data.qrCode}`;
                        showQrSection('image');
                    } else {
                        const qrSpinnerText = document.getElementById('qrSpinnerText');
                        if (qrSpinnerText) qrSpinnerText.textContent = 'Generando imagen del código QR...';
                        showQrSection('spinner');
                    }
                    startQrPolling();

                } else if (status === 'INITIALIZING' || status === 'AUTHENTICATING') {
                    statusBadge.className = 'badge badge-pill badge-info px-3 py-2';
                    statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> INICIANDO MOTOR (' + status + ')';
                    const qrSpinnerText = document.getElementById('qrSpinnerText');
                    if (qrSpinnerText) {
                        qrSpinnerText.textContent = status === 'AUTHENTICATING' 
                            ? '🔑 Autenticando con WhatsApp...' 
                            : '🚀 Iniciando navegador de WhatsApp... El código QR aparecerá en unos segundos.';
                    }
                    showQrSection('spinner');
                    startQrPolling();

                } else if (status === 'CREATED') {
                    statusBadge.className = 'badge badge-pill badge-secondary px-3 py-2';
                    statusBadge.innerHTML = '<i class="fas fa-plus-circle mr-1"></i> SESIÓN CREADA (CREATED)';
                    const qrSpinnerText = document.getElementById('qrSpinnerText');
                    if (qrSpinnerText) qrSpinnerText.textContent = 'Iniciando motor de WhatsApp para generar QR...';
                    showQrSection('spinner');

                    fetch(routes.startSession, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ session_id: currentSessionId })
                    }).catch(e => {});

                    startQrPolling();

                } else {
                    statusBadge.className = 'badge badge-pill badge-secondary px-3 py-2';
                    statusBadge.innerHTML = '<i class="fas fa-power-off mr-1"></i> DESCONECTADO (' + status + ')';
                    showQrSection('disconnected');
                    stopQrPolling();
                }
            })
            .catch(err => {
                stopQrPolling();
            });
    }

    function startQrPolling() {
        if (!qrPollInterval) {
            qrPollInterval = setInterval(checkQrAndStatus, 3000);
        }
    }

    function stopQrPolling() {
        if (qrPollInterval) {
            clearInterval(qrPollInterval);
            qrPollInterval = null;
        }
    }

    // Eventos de botones
    document.getElementById('sessionSelect').addEventListener('change', function() {
        currentSessionId = this.value;
        checkQrAndStatus();
    });

    document.getElementById('btnRefreshSession').addEventListener('click', function() {
        loadSessions();
    });

    document.getElementById('btnStartSession').addEventListener('click', function() {
        if (!currentSessionId) {
            showAlert('warning', 'Por favor selecciona o crea una sesión primero.');
            return;
        }
        showAlert('info', 'Iniciando motor de WhatsApp...');
        fetch(routes.startSession, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session_id: currentSessionId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                checkQrAndStatus();
            } else {
                showAlert('danger', data.message || 'No se pudo iniciar la sesión.');
            }
        })
        .catch(err => showAlert('danger', 'Error al iniciar sesión.'));
    });

    document.getElementById('btnStopSession').addEventListener('click', function() {
        if (!currentSessionId) return;
        if (!confirm('¿Estás seguro de detener la sesión de WhatsApp?')) return;

        fetch(routes.stopSession, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session_id: currentSessionId })
        })
        .then(res => res.json())
        .then(data => {
            showAlert('info', data.message);
            checkQrAndStatus();
        });
    });

    document.getElementById('btnSetActiveSession').addEventListener('click', function() {
        if (!currentSessionId) return;
        fetch(routes.setActive, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ session_id: currentSessionId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('activeSessionBadge').textContent = `Activa actual: ${currentSessionId}`;
                showAlert('success', 'Sesión establecida como activa para el envío y recepción de mensajes.');
            }
        });
    });

    document.getElementById('btnSubmitCreateSession').addEventListener('click', function() {
        const name = document.getElementById('newSessionName').value.trim();
        if (!name) {
            alert('Por favor ingresa un nombre para la sesión');
            return;
        }

        fetch(routes.createSession, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(res => res.json())
        .then(data => {
            $('#modalCreateSession').modal('hide');
            if (data.success) {
                showAlert('success', 'Sesión creada. Iniciando proceso de conexión...');
                loadSessions();
            } else {
                showAlert('danger', data.message);
            }
        });
    });

    // Cargar sesiones al abrir la página
    loadSessions();
});
</script>

@endsection
