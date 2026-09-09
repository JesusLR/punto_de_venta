@extends("maestra")
@section("titulo", "Centro de Mensajes de WhatsApp")
@section("contenido")

<style>
    /* Estilos Principales del Módulo de Mensajería */
    .chat-wrapper {
        height: calc(100vh - 130px);
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.1);
        display: flex;
        border: 1px solid #e2e8f0;
    }

    /* Columna Izquierda: Lista de Chats */
    .chat-sidebar {
        width: 360px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .chat-sidebar-header {
        padding: 1.25rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border-bottom: 2px solid #D4AF37;
    }

    .chat-search-box {
        position: relative;
        margin-top: 0.75rem;
    }

    .chat-search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .chat-search-box input {
        padding-left: 36px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.85rem;
    }

    .chat-search-box input::placeholder {
        color: #94a3b8;
    }

    .chat-search-box input:focus {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: none;
        border-color: #D4AF37;
    }

    /* Tabs de Filtro */
    .chat-filter-tabs {
        display: flex;
        background: #f8fafc;
        padding: 6px;
        border-bottom: 1px solid #e2e8f0;
        gap: 4px;
    }

    .chat-filter-btn {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        padding: 6px 10px;
        border-radius: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .chat-filter-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .chat-list {
        flex-grow: 1;
        overflow-y: auto;
    }

    .chat-item {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .chat-item:hover {
        background-color: #f8fafc;
    }

    .chat-item.active {
        background-color: #f1f5f9;
        border-left: 4px solid #25D366;
    }

    .chat-avatar-container {
        position: relative;
    }

    .chat-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.15rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .status-indicator.bot {
        background-color: #10b981;
    }

    .status-indicator.agent {
        background-color: #f59e0b;
    }

    .chat-info {
        flex-grow: 1;
        min-width: 0;
    }

    .chat-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.92rem;
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

    /* Columna Derecha: Hilo Principal */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
    }

    .chat-header {
        padding: 1rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        z-index: 10;
    }

    .chat-thread {
        flex-grow: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #efeae2 url('https://user-images.githubusercontent.com/15075759/28719144-86ece05e-7696-11e7-9364-2c7ef85100d4.png') repeat;
    }

    /* Burbujas de Mensaje */
    .message-group {
        display: flex;
        flex-direction: column;
    }

    .message-bubble {
        max-width: 68%;
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 0.88rem;
        line-height: 1.45;
        position: relative;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        word-break: break-word;
        white-space: pre-wrap;
    }

    .message-inbound {
        background: #ffffff;
        color: #0f172a;
        align-self: flex-start;
        border-top-left-radius: 2px;
        border: 1px solid #e2e8f0;
    }

    .message-outbound {
        background: #dcf8c6;
        color: #0f172a;
        align-self: flex-end;
        border-top-right-radius: 2px;
    }

    .message-sender {
        font-size: 0.75rem;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .message-time {
        font-size: 0.68rem;
        color: #94a3b8;
        margin-top: 4px;
        text-align: right;
        display: block;
    }

    /* Barra de Respuestas Rápidas */
    .quick-replies-bar {
        padding: 8px 16px;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        white-space: nowrap;
    }

    .quick-reply-chip {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .quick-reply-chip:hover {
        background: #25D366;
        color: white;
        border-color: #25D366;
    }

    .chat-footer {
        padding: 1rem 1.25rem;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
    }

    .send-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        border-radius: 50%;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }

    /* Pulse animation for active bot */
    .pulse-green {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 1.6s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<div class="container-fluid px-0 px-md-3">
    <div class="chat-wrapper" id="chatWrapper">
        <!-- Columna Izquierda: Lista de Conversaciones -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-white mb-0">
                        <i class="fab fa-whatsapp text-success mr-2"></i> Centro de Mensajes
                    </h5>
                    <span class="badge badge-success px-2 py-1" style="border-radius: 10px; font-size: 0.7rem;">
                        <span class="pulse-green mr-1"></span> En Vivo
                    </span>
                </div>
                <div class="chat-search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchChat" onkeyup="filterChats()" placeholder="Buscar cliente o número...">
                </div>
            </div>

            <!-- Filtros de Estado -->
            <div class="chat-filter-tabs">
                <button class="chat-filter-btn active" onclick="setFilter('all', this)">Todos (<span id="countAll">{{ count($conversaciones) }}</span>)</button>
                <button class="chat-filter-btn" onclick="setFilter('bot', this)">🤖 Bot Activo</button>
                <button class="chat-filter-btn" onclick="setFilter('agent', this)">👩‍💻 Agente</button>
            </div>

            <div class="chat-list" id="chatList">
                @forelse($conversaciones as $conv)
                    <div class="chat-item" data-id="{{ $conv->id }}" data-step="{{ $conv->step }}" data-search="{{ strtolower(($conv->cliente ? $conv->cliente->nombre : '') . ' ' . $conv->phone . ' ' . $conv->chat_id) }}" onclick="loadConversation({{ $conv->id }})">
                        <div class="chat-avatar-container">
                            <div class="chat-avatar">
                                {{ strtoupper(substr($conv->cliente ? $conv->cliente->nombre : 'L', 0, 1)) }}
                            </div>
                            <div class="status-indicator {{ $conv->step === 'agent_active' ? 'agent' : 'bot' }}"></div>
                        </div>
                        <div class="chat-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="chat-name">{{ $conv->cliente ? $conv->cliente->nombre : 'Lead Anónimo' }}</span>
                                <span class="small text-muted" style="font-size: 0.68rem;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                            </div>
                            <div class="chat-last-msg">
                                {{ $conv->lastMessage ? $conv->lastMessage->body : 'Sin mensajes' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted small">
                        <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">No hay conversaciones registradas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Columna Derecha: Hilo de Chat -->
        <div class="chat-main">
            <!-- Header del Chat Abierto -->
            <div class="chat-header" id="chatHeader" style="display: none;">
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-light d-md-none mr-2" onclick="closeMobileChat()">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="chat-avatar mr-3" id="activeAvatar">A</div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="font-weight-bold text-dark mb-0 mr-2" id="activeName">Selecciona un chat</h6>
                            <span id="activeStatusBadge" class="badge"></span>
                        </div>
                        <small class="text-muted"><i class="fas fa-phone-alt mr-1 text-success"></i> <span id="activePhone"></span></small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm shadow-sm font-weight-bold" id="btnToggleBot" onclick="toggleBot()" style="border-radius: 8px;">
                        <i class="fas fa-robot mr-1"></i> <span id="botBtnText">Pausar Bot</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger shadow-sm font-weight-bold ml-2" onclick="deleteActiveConversation()" style="border-radius: 8px;" title="Eliminar conversación">
                        <i class="fas fa-trash-alt mr-1"></i> Eliminar
                    </button>
                </div>
            </div>

            <!-- Placeholder Vacío -->
            <div id="noChatSelected" class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-4">
                <div class="p-4 bg-white rounded-circle shadow-sm mb-3">
                    <i class="fab fa-whatsapp text-success" style="font-size: 4rem;"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-1">Centro de Mensajería en Vivo</h4>
                <p class="text-muted small" style="max-width: 400px;">
                    Selecciona un chat de la lista izquierda para atender clientes, revisar cotizaciones enviadas o enviar respuestas personalizadas.
                </p>
            </div>

            <!-- Hilo de Mensajes -->
            <div class="chat-thread" id="chatThread" style="display: none;"></div>

            <!-- Respuestas Rápidas (Pills) -->
            <div class="quick-replies-bar" id="quickRepliesBar" style="display: none;">
                <span class="quick-reply-chip" onclick="insertQuickReply('👋 ¡Hola! Bienvenido(a) a Joyería Colibrí, ¿en qué podemos ayudarte hoy?')">
                    👋 Saludo Inicial
                </span>
                <span class="quick-reply-chip" onclick="insertQuickReply('🛍️ Con gusto te proporcionamos información de nuestro catálogo de joyas.')">
                    🛍️ Catálogo
                </span>
                <span class="quick-reply-chip" onclick="insertQuickReply('💍 El precio del oro del día está actualizado en nuestro sistema.')">
                    💍 Cotización Oro
                </span>
                <span class="quick-reply-chip" onclick="insertQuickReply('📋 Por favor proporciónanos tu folio o nombre completo para consultar tu apartado.')">
                    📋 Consultar Apartado
                </span>
                <span class="quick-reply-chip" onclick="insertQuickReply('📍 Nos encontramos en Calle 27 s/n Centro, Progreso, Yucatán. ¡Te esperamos!')">
                    📍 Ubicación
                </span>
            </div>

            <!-- Formulario de Respuesta -->
            <div class="chat-footer" id="chatFooter" style="display: none;">
                <form id="sendForm" onsubmit="submitMessage(event)" class="d-flex align-items-center gap-2">
                    <input type="text" id="messageInput" class="form-control py-3" placeholder="Escribe un mensaje..." autocomplete="off" required style="border-radius: 24px; background: #f8fafc; border: 1px solid #cbd5e1;">
                    <button type="submit" class="send-btn ml-2" title="Enviar Mensaje">
                        <i class="fas fa-paper-plane" style="font-size: 1.1rem;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let activeConversationId = null;
    let activeConversationStep = null;
    let currentFilter = 'all';

    function loadConversation(id) {
        activeConversationId = id;
        document.getElementById("chatWrapper").classList.add("mobile-active");

        // Resaltar item activo
        document.querySelectorAll(".chat-item").forEach(el => el.classList.remove("active"));
        const activeItem = document.querySelector(`.chat-item[data-id="${id}"]`);
        if (activeItem) activeItem.classList.add("active");

        fetch(`/whatsapp/chat/${id}/messages`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                const conv = data.conversation;
                activeConversationStep = conv.step;

                // Mostrar vistas
                document.getElementById("noChatSelected").style.display = "none";
                document.getElementById("chatHeader").style.display = "flex";
                document.getElementById("chatThread").style.display = "flex";
                document.getElementById("quickRepliesBar").style.display = "flex";
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
                    thread.innerHTML = `<div class="text-center my-auto text-muted small">No hay historial previo registrado en este chat.</div>`;
                } else {
                    data.messages.forEach(msg => {
                        const isOutbound = msg.direction === 'outbound';
                        const bubble = document.createElement("div");
                        bubble.className = `message-bubble ${isOutbound ? 'message-outbound' : 'message-inbound'}`;
                        bubble.innerHTML = `
                            <div class="message-sender ${isOutbound ? 'text-success' : 'text-primary'}">${escapeHtml(msg.sender_name)}</div>
                            <div>${escapeHtml(msg.body)}</div>
                            <span class="message-time"><i class="far fa-clock mr-1"></i> ${msg.time}</span>
                        `;
                        thread.appendChild(bubble);
                    });
                }

                thread.scrollTop = thread.scrollHeight;
            });
    }

    function updateBotButtonState(step) {
        const btn = document.getElementById("btnToggleBot");
        const btnText = document.getElementById("botBtnText");
        const badge = document.getElementById("activeStatusBadge");

        if (step === 'agent_active') {
            btn.className = "btn btn-sm btn-warning text-dark shadow-sm font-weight-bold";
            btnText.innerText = "🤖 Reactivar Bot";
            badge.className = "badge badge-warning text-dark";
            badge.innerText = "👩‍💻 Atendiendo por Agente";
        } else {
            btn.className = "btn btn-sm btn-outline-secondary shadow-sm font-weight-bold";
            btnText.innerText = "⏸️ Pausar Bot";
            badge.className = "badge badge-success";
            badge.innerText = "🤖 Bot Automático Activo";
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
                    title: newStatus === 'active' ? 'Bot Reactivado' : 'Bot Pausado',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    function insertQuickReply(text) {
        const input = document.getElementById("messageInput");
        input.value = text;
        input.focus();
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
                loadConversation(activeConversationId);
            } else {
                Swal.fire('Error', data.message || 'No se pudo enviar el mensaje', 'error');
            }
        });
    }

    function deleteActiveConversation() {
        if (!activeConversationId) return;

        Swal.fire({
            title: '¿Eliminar conversación?',
            text: "Se borrará permanentemente la conversación y todo el historial.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/whatsapp/chat/${activeConversationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Eliminado', data.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                    }
                });
            }
        });
    }

    function filterChats() {
        const query = document.getElementById("searchChat").value.toLowerCase();
        document.querySelectorAll(".chat-item").forEach(item => {
            const searchText = item.dataset.search || "";
            const step = item.dataset.step || "";

            let matchesSearch = searchText.includes(query);
            let matchesFilter = true;

            if (currentFilter === 'bot') {
                matchesFilter = step !== 'agent_active';
            } else if (currentFilter === 'agent') {
                matchesFilter = step === 'agent_active';
            }

            if (matchesSearch && matchesFilter) {
                item.style.display = "flex";
            } else {
                item.style.display = "none";
            }
        });
    }

    function setFilter(filter, btn) {
        currentFilter = filter;
        document.querySelectorAll(".chat-filter-btn").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
        filterChats();
    }

    function closeMobileChat() {
        document.getElementById("chatWrapper").classList.remove("mobile-active");
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Polling cada 4 segundos
    setInterval(() => {
        if (activeConversationId) {
            loadConversation(activeConversationId);
        }
    }, 4000);
</script>

@endsection
