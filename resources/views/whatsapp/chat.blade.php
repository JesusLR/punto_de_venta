@extends("maestra")
@section("titulo", "Centro de Mensajes de WhatsApp")
@section("contenido")

<style>
    .chat-container {
        height: calc(100vh - 120px);
        background: #efeae2;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
    }

    /* Lista Lateral de Conversaciones */
    .chat-sidebar {
        width: 340px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .chat-sidebar-header {
        padding: 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .chat-list {
        flex-grow: 1;
        overflow-y: auto;
    }

    .chat-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-item:hover, .chat-item.active {
        background-color: #f1f5f9;
    }

    .chat-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .chat-info {
        flex-grow: 1;
        min-width: 0;
    }

    .chat-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.95rem;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-last-msg {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Área Principal de Chat */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: #efeae2 url('https://user-images.githubusercontent.com/15075759/28719144-86ece05e-7696-11e7-9364-2c7ef85100d4.png') repeat;
    }

    .chat-header {
        padding: 1rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-thread {
        flex-grow: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* Burbujas de Mensajes */
    .message-bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 0.9rem;
        line-height: 1.4;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        word-break: break-word;
        white-space: pre-wrap;
    }

    .message-inbound {
        background: #ffffff;
        color: #0f172a;
        align-self: flex-start;
        border-top-left-radius: 2px;
    }

    .message-outbound {
        background: #dcf8c6;
        color: #0f172a;
        align-self: flex-end;
        border-top-right-radius: 2px;
    }

    .message-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 4px;
        text-align: right;
        display: block;
    }

    .chat-footer {
        padding: 1rem;
        background: #f0f2f5;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 768px) {
        .chat-sidebar {
            width: 100%;
        }
        .chat-main {
            display: none;
        }
        .chat-container.mobile-active .chat-sidebar {
            display: none;
        }
        .chat-container.mobile-active .chat-main {
            display: flex;
        }
    }
</style>

<div class="container-fluid px-0">
    <div class="chat-container" id="chatContainer">
        <!-- Sidebar: Lista de Conversaciones -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h5 class="font-weight-bold text-dark mb-2">
                    <i class="fab fa-whatsapp text-success mr-1"></i> Chats de WhatsApp
                </h5>
                <input type="text" id="searchChat" class="form-control form-control-sm" placeholder="Buscar por nombre o teléfono...">
            </div>

            <div class="chat-list" id="chatList">
                @forelse($conversaciones as $conv)
                    <div class="chat-item" data-id="{{ $conv->id }}" onclick="loadConversation({{ $conv->id }})">
                        <div class="chat-avatar">
                            {{ strtoupper(substr($conv->cliente ? $conv->cliente->nombre : 'L', 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="chat-name">{{ $conv->cliente ? $conv->cliente->nombre : 'Lead Anónimo' }}</span>
                                <span class="small text-muted" style="font-size: 0.7rem;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                            </div>
                            <div class="chat-last-msg">
                                {{ $conv->lastMessage ? $conv->lastMessage->body : 'Sin mensajes' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted small">
                        No hay conversaciones registradas aún.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Thread -->
        <div class="chat-main">
            <!-- Header del Chat Abierto -->
            <div class="chat-header" id="chatHeader" style="display: none;">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-light d-md-none mr-2" onclick="closeMobileChat()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="chat-avatar mr-3" id="activeAvatar">A</div>
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0" id="activeName">Selecciona un chat</h6>
                        <small class="text-muted" id="activePhone"></small>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-sm shadow-sm" id="btnToggleBot" onclick="toggleBot()">
                        <i class="fas fa-robot mr-1"></i> <span id="botBtnText">Pausar Bot</span>
                    </button>
                </div>
            </div>

            <!-- Placeholder cuando no hay chat seleccionado -->
            <div id="noChatSelected" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                <i class="fab fa-whatsapp text-success mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                <h5 class="font-weight-bold text-dark">Centro de Mensajería en Vivo</h5>
                <p class="text-muted small">Selecciona una conversación de la columna izquierda para ver el historial y responder en directo.</p>
            </div>

            <!-- Hilo de Mensajes -->
            <div class="chat-thread" id="chatThread" style="display: none;"></div>

            <!-- Footer: Formulario de Respuesta -->
            <div class="chat-footer" id="chatFooter" style="display: none;">
                <form id="sendForm" onsubmit="submitMessage(event)">
                    <div class="input-group">
                        <textarea id="messageInput" class="form-control" rows="1" placeholder="Escribe un mensaje..." required style="resize: none; border-radius: 20px 0 0 20px;"></textarea>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-success px-4" style="border-radius: 0 20px 20px 0;">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let activeConversationId = null;
    let activeConversationStep = null;

    function loadConversation(id) {
        activeConversationId = id;
        document.getElementById("chatContainer").classList.add("mobile-active");

        // Resaltar item activo en la lista
        document.querySelectorAll(".chat-item").forEach(el => el.classList.remove("active"));
        const activeItem = document.querySelector(`.chat-item[data-id="${id}"]`);
        if (activeItem) activeItem.classList.add("active");

        fetch(`/whatsapp/chat/${id}/messages`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                const conv = data.conversation;
                activeConversationStep = conv.step;

                // Mostrar elementos del chat
                document.getElementById("noChatSelected").style.display = "none";
                document.getElementById("chatHeader").style.display = "flex";
                document.getElementById("chatThread").style.display = "flex";
                document.getElementById("chatFooter").style.display = "block";

                // Actualizar Header
                document.getElementById("activeName").innerText = conv.nombre;
                document.getElementById("activePhone").innerText = conv.phone || conv.chat_id;
                document.getElementById("activeAvatar").innerText = conv.nombre.charAt(0).toUpperCase();

                updateBotButtonState(conv.step);

                // Renderizar Mensajes
                const thread = document.getElementById("chatThread");
                thread.innerHTML = "";

                if (data.messages.length === 0) {
                    thread.innerHTML = `<div class="text-center my-auto text-muted small">No hay historial de mensajes previa en esta conversación.</div>`;
                } else {
                    data.messages.forEach(msg => {
                        const isOutbound = msg.direction === 'outbound';
                        const bubble = document.createElement("div");
                        bubble.className = `message-bubble ${isOutbound ? 'message-outbound' : 'message-inbound'}`;
                        bubble.innerHTML = `
                            <div class="small font-weight-bold mb-1 ${isOutbound ? 'text-success' : 'text-primary'}">${msg.sender_name}</div>
                            <div>${escapeHtml(msg.body)}</div>
                            <span class="message-time">${msg.time}</span>
                        `;
                        thread.appendChild(bubble);
                    });
                }

                // Scroll al último mensaje
                thread.scrollTop = thread.scrollHeight;
            });
    }

    function updateBotButtonState(step) {
        const btn = document.getElementById("btnToggleBot");
        const btnText = document.getElementById("botBtnText");

        if (step === 'agent_active') {
            btn.className = "btn btn-sm btn-warning text-dark shadow-sm";
            btnText.innerText = "🤖 Reactivar Bot Automático";
        } else {
            btn.className = "btn btn-sm btn-outline-secondary shadow-sm";
            btnText.innerText = "⏸️ Pausar Bot (Atención Agente)";
        }
    }

    function toggleBot() {
        if (!activeConversationId) return;

        const newStatus = activeConversationStep === 'agent_active' ? 'active' : 'paused';

        fetch('/whatsapp/chat/toggle-bot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                conversation_id: activeConversationId,
                status: newStatus
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                activeConversationStep = data.new_step;
                updateBotButtonState(data.new_step);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: newStatus === 'active' ? 'Bot Automático Reactivado' : 'Bot Pausado para Atención Humana',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    function submitMessage(e) {
        e.preventDefault();
        const input = document.getElementById("messageInput");
        const body = input.value.trim();

        if (!body || !activeConversationId) return;

        input.value = "";

        fetch('/whatsapp/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                conversation_id: activeConversationId,
                body: body
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Recargar hilo
                loadConversation(activeConversationId);
            } else {
                Swal.fire('Error', data.message || 'No se pudo enviar el mensaje', 'error');
            }
        });
    }

    function closeMobileChat() {
        document.getElementById("chatContainer").classList.remove("mobile-active");
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Refresco periódico cada 4 segundos
    setInterval(() => {
        if (activeConversationId) {
            loadConversation(activeConversationId);
        }
    }, 4000);
</script>

@endsection
