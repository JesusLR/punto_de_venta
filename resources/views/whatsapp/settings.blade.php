@extends("maestra")
@section("titulo", "Configuración de WhatsApp Bot")
@section("contenido")

<div class="container-fluid px-0 px-md-3">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 p-4 rounded-lg shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 5px solid #D4AF37;">
        <div>
            <h2 class="h3 font-weight-bold text-white mb-1">
                <i class="fas fa-robot text-warning mr-2"></i> Configuración del Chatbot de WhatsApp
            </h2>
            <p class="text-muted mb-0 small" style="color: #94a3b8 !important;">
                Administra el estado de respuestas automáticas, mensajes de bienvenida y conexión de webhook.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="fas fa-sliders-h text-primary mr-2"></i> Parámetros Principales del Bot
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.settings.update') }}" method="POST">
                        @csrf

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Estado del Bot</label>
                            <select name="wa_bot_enabled" class="form-control">
                                <option value="1" {{ ($settings['wa_bot_enabled'] ?? '1') == '1' ? 'selected' : '' }}>🟢 Habilitado (Responde automáticamente mensajes entrantes)</option>
                                <option value="0" {{ ($settings['wa_bot_enabled'] ?? '1') == '0' ? 'selected' : '' }}>🔴 Deshabilitado (No enviar respuestas automáticas)</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Número de WhatsApp Principal de la Tienda</label>
                            <input type="text" name="wa_phone_number" class="form-control" placeholder="Ej: 5219998887766" value="{{ $settings['wa_phone_number'] }}">
                            <small class="form-text text-muted">Teléfono registrado en WhatsApp Business o la API de OpenWA.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Mensaje de Saludo Personalizado (Opcional)</label>
                            <textarea name="wa_welcome_text" class="form-control" rows="3" placeholder="👋 ¡Hola! Bienvenido a nuestra tienda. (Si se deja vacío, se usará el saludo estándar)">{{ $settings['wa_welcome_text'] }}</textarea>
                            <small class="form-text text-muted">Este mensaje encabeza el menú interactivo enviado a clientes nuevos.</small>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;">
                                <i class="fas fa-save mr-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-lg mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="fas fa-network-wired text-success mr-2"></i> Estado de la API OpenWA
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2"><strong>Servidor de Mensajería:</strong></p>
                    <div class="p-2 bg-light rounded text-monospace small mb-3 border">
                        http://74.208.53.13:2785
                    </div>

                    <p class="small text-muted mb-2"><strong>URL de Webhook Entrante (OpenWA):</strong></p>
                    <div class="p-2 bg-light rounded text-monospace small mb-3 border text-break">
                        {{ url('/api/whatsapp/webhook') }}
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Asegúrate de que OpenWA esté configurado para enviar sus notificaciones HTTP POST a la URL de webhook anterior.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
