@extends("maestra")
@section("titulo", "Generador de Enlaces de WhatsApp")
@section("contenido")

<div class="container-fluid px-0 px-md-3">
    <!-- Encabezado con estética moderna -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 p-4 rounded-lg shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 5px solid #25D366;">
        <div>
            <h2 class="h3 font-weight-bold text-white mb-1">
                <i class="fab fa-whatsapp text-success mr-2"></i> Generador de Enlaces y QR de WhatsApp
            </h2>
            <p class="text-muted mb-0 small" style="color: #94a3b8 !important;">
                Crea enlaces personalizados para redes sociales o QR para folletos. El chatbot responderá automáticamente al hacer clic.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('whatsapp.settings.index') }}" class="btn btn-outline-light btn-sm shadow-sm" style="border-radius: 8px;">
                <i class="fas fa-cog mr-1" style="color: #D4AF37;"></i> Ajustes del Bot
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

    <div class="row">
        <!-- Panel 1: Creador de Enlaces -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom d-flex align-items-center">
                    <i class="fas fa-link text-primary mr-2"></i> Configurar Enlace de Campaña
                </div>
                <div class="card-body">
                    <form id="formGenerator">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small">Número de WhatsApp de la Tienda</label>
                            <input type="text" id="phoneInput" class="form-control" placeholder="Ej: 5219998887766" value="{{ $storePhone }}">
                            <small class="form-text text-muted">Incluye la clave de país sin signos (ej. 521 para México).</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small">Acción / Palabra Clave del Bot</label>
                            <select id="keywordSelect" class="form-control">
                                <option value="CATALOGO">🛍️ CATALOGO - Consultar Productos e Inventario</option>
                                <option value="ORO">💍 ORO - Consultar Cotización del Día</option>
                                <option value="APARTADO">📋 APARTADO - Consultar Saldo o Abonos</option>
                                <option value="AGENTE">👩‍💻 AGENTE - Hablar con Asesor Humano</option>
                                <option value="CUSTOM">✏️ Mensaje Personalizado</option>
                            </select>
                        </div>

                        <div class="form-group mb-3" id="customTextContainer" style="display: none;">
                            <label class="font-weight-bold text-dark small">Mensaje Personalizado de Entrada</label>
                            <textarea id="customTextInput" class="form-control" rows="3" placeholder="Ej: Hola, vi su anuncio en Facebook y me interesa información del producto X"></textarea>
                        </div>

                        <hr class="my-4">

                        <!-- Resultado -->
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark small">Enlace Generado</label>
                            <div class="input-group">
                                <input type="text" id="generatedUrl" class="form-control bg-light" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" id="btnCopy">
                                        <i class="far fa-copy mr-1"></i> Copiar Enlace
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel 2: Código QR & Vista Previa -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm rounded-lg text-center h-100">
                <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                    <i class="fas fa-qrcode text-dark mr-2"></i> Código QR de Escaneo
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="p-3 bg-white rounded shadow-sm border mb-3">
                        <img id="qrImage" src="" alt="Código QR WhatsApp" style="width: 200px; height: 200px;" class="img-fluid">
                    </div>
                    <p class="small text-muted mb-3">Escanea este código con cualquier cámara de smartphone para probar la interacción con el Bot.</p>
                    <a id="btnTestUrl" href="#" target="_blank" class="btn btn-outline-success btn-block" style="border-radius: 8px;">
                        <i class="fab fa-whatsapp mr-1"></i> Probar en WhatsApp Web / App
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla: Historial de Chats y Leads Automáticos -->
    <div class="card border-0 shadow-sm rounded-lg mb-4">
        <div class="card-header bg-white font-weight-bold py-3 border-bottom d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users text-info mr-2"></i> Registro de Leads y Conversaciones Recientes</span>
            <span class="badge badge-primary px-3 py-2" style="border-radius: 12px;">{{ count($conversaciones) }} Activas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Cliente / Lead</th>
                            <th>Teléfono</th>
                            <th>Estado Bot</th>
                            <th>Última Palabra Clave</th>
                            <th>Última Interacción</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversaciones as $conv)
                            <tr id="row-conv-{{ $conv->id }}">
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-user-circle text-muted mr-1"></i>
                                    {{ $conv->cliente ? $conv->cliente->nombre : 'Lead Anónimo' }}
                                </td>
                                <td>{{ $conv->phone ?: $conv->chat_id }}</td>
                                <td>
                                    @if($conv->step === 'agent_active')
                                        <span class="badge badge-warning text-dark"><i class="fas fa-headset mr-1"></i> Agente Humano</span>
                                    @else
                                        <span class="badge badge-success"><i class="fas fa-robot mr-1"></i> Bot Activo ({{ $conv->step }})</span>
                                    @endif
                                </td>
                                <td><code>{{ $conv->last_keyword ?: 'INICIO' }}</code></td>
                                <td class="small text-muted">{{ $conv->updated_at->diffForHumans() }}</td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="deleteConversationTable({{ $conv->id }})" title="Eliminar conversación">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No hay conversaciones registradas aún. Al recibir el primer mensaje vía WhatsApp se registrará aquí.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const phoneInput = document.getElementById("phoneInput");
        const keywordSelect = document.getElementById("keywordSelect");
        const customTextContainer = document.getElementById("customTextContainer");
        const customTextInput = document.getElementById("customTextInput");
        const generatedUrl = document.getElementById("generatedUrl");
        const qrImage = document.getElementById("qrImage");
        const btnTestUrl = document.getElementById("btnTestUrl");
        const btnCopy = document.getElementById("btnCopy");

        function updateLink() {
            const phone = phoneInput.value.replace(/[^0-9]/g, "");
            let text = keywordSelect.value;

            if (text === "CUSTOM") {
                customTextContainer.style.display = "block";
                text = customTextInput.value || "Hola, me interesa obtener información";
            } else {
                customTextContainer.style.display = "none";
                text = "Hola, me interesa la opción: " + text;
            }

            const encodedText = encodeURIComponent(text);
            const fullUrl = `https://wa.me/${phone}?text=${encodedText}`;

            generatedUrl.value = fullUrl;
            btnTestUrl.href = fullUrl;

            // Generar QR
            qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(fullUrl)}`;
        }

        phoneInput.addEventListener("input", updateLink);
        keywordSelect.addEventListener("change", updateLink);
        customTextInput.addEventListener("input", updateLink);

        btnCopy.addEventListener("click", () => {
            generatedUrl.select();
            document.execCommand("copy");
            Swal.fire({
                icon: 'success',
                title: '¡Enlace copiado!',
                text: 'El link de WhatsApp ha sido copiado al portapapeles.',
                timer: 2000,
                showConfirmButton: false
            });
        });

        updateLink();
    });

    function deleteConversationTable(id) {
        Swal.fire({
            title: '¿Eliminar conversación?',
            text: "Se borrará permanentemente la conversación y su historial de mensajes.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/whatsapp/chat/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById(`row-conv-${id}`);
                        if (row) row.remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                    }
                });
            }
        });
    }
</script>

@endsection
