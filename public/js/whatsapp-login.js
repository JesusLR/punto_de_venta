(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('whatsappLogin');
        if (!root) return;
        var select = document.getElementById('sessionSelect');
        var current = '';
        var poll = null;
        var checking = false;
        var busy = false;
        var names = {};

        function alertMessage(message, type) {
            var alert = document.createElement('div');
            alert.className = 'alert alert-' + (type || 'info');
            alert.textContent = message;
            document.getElementById('ajaxAlertContainer').replaceChildren(alert);
        }

        function request(url, data) {
            return fetch(url, {
                method: data ? 'POST' : 'GET',
                headers: {
                    'Accept': 'application/json', 'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: data ? JSON.stringify(data) : undefined
            }).then(function (response) {
                return response.json().catch(function () { return {}; }).then(function (result) {
                    if (!response.ok || result.success === false) {
                        var validation = result.errors ? Object.values(result.errors).reduce(function (all, messages) { return all.concat(messages); }, []).join(' ') : '';
                        throw new Error(validation || result.message || (response.status === 419 ? 'La sesión expiró. Recarga la página.' : 'No se pudo completar la solicitud de WhatsApp.'));
                    }
                    return result;
                });
            });
        }

        function buttons() {
            ['btnSetActiveSession', 'btnStartSession', 'btnStopSession'].forEach(function (id) {
                document.getElementById(id).disabled = busy || !current;
            });
            select.disabled = busy;
            document.getElementById('btnRefreshSession').disabled = busy;
            document.getElementById('btnSubmitCreateSession').disabled = busy;
        }

        function stopPolling() {
            clearTimeout(poll);
            poll = null;
        }

        function state(status, phone, account, qr) {
            var labels = { READY: 'Conectado', QR_READY: 'Escanea el código QR', INITIALIZING: 'Iniciando sesión…', AUTHENTICATING: 'Vinculando WhatsApp…', CREATED: 'Sesión creada', DISCONNECTED: 'Desconectado', ACTION_REQUIRED: 'Revisa WhatsApp en tu teléfono', FAILED: 'No se pudo iniciar la sesión' };
            var badge = document.getElementById('statusBadge');
            badge.textContent = labels[status] || 'Sin sesión';
            badge.className = 'badge mt-3 px-3 py-2 badge-' + (status === 'READY' ? 'success' : (status === 'QR_READY' ? 'warning' : 'secondary'));
            document.getElementById('infoPhone').textContent = phone || '—';
            document.getElementById('infoPushName').textContent = account || '—';
            var image = document.getElementById('qrImage');
            image.hidden = !qr;
            if (qr) image.src = qr;
            else image.removeAttribute('src');
            var message = document.getElementById('qrMessage');
            message.hidden = !!qr;
            message.textContent = status === 'READY' ? 'WhatsApp está listo para enviar documentos.' : (labels[status] || 'Selecciona o crea una sesión para comenzar.');
        }

        function check() {
            if (!current || checking) return Promise.resolve();
            var id = current;
            checking = true;
            stopPolling();
            return request(root.dataset.qr + '/' + encodeURIComponent(id) + '/qr').then(function (data) {
                if (id !== current) return;
                state(data.status, data.phone, data.pushName, data.qrCode);
                if (['INITIALIZING', 'AUTHENTICATING', 'QR_READY'].indexOf(data.status) >= 0) poll = setTimeout(check, 3000);
            }).catch(function (error) {
                if (id === current) alertMessage(error.message, 'danger');
            }).finally(function () {
                checking = false;
                if (id !== current && current) check();
            });
        }

        function load() {
            stopPolling();
            return request(root.dataset.list).then(function (data) {
                select.replaceChildren();
                names = {};
                data.sessions.forEach(function (session) {
                    var option = document.createElement('option');
                    option.value = session.id;
                    option.textContent = session.name + (session.phone ? ' · ' + session.phone : '');
                    names[session.id] = session.name;
                    select.appendChild(option);
                });
                if (!data.sessions.length) {
                    var empty = document.createElement('option');
                    empty.value = '';
                    empty.textContent = 'No hay sesiones. Crea una para vincular WhatsApp.';
                    select.appendChild(empty);
                }
                current = data.active_session_id || '';
                select.value = current;
                document.getElementById('activeSessionBadge').textContent = current ? 'Sesión para envíos: ' + names[current] : 'Solo se muestran las sesiones de este sistema.';
                state('');
                buttons();
                return check();
            }).catch(function (error) {
                current = '';
                select.replaceChildren(new Option('No se pudieron cargar las sesiones', ''));
                state('');
                buttons();
                alertMessage(error.message, 'danger');
            });
        }

        function action(kind) {
            if (busy || !current) return;
            if (kind === 'stop' && !confirm('¿Cerrar la sesión de WhatsApp? Necesitarás vincularla nuevamente para enviar documentos.')) return;
            busy = true;
            stopPolling();
            buttons();
            request(root.dataset[kind], { session_id: current }).then(function (data) {
                alertMessage(data.message, 'success');
                if (kind === 'active') document.getElementById('activeSessionBadge').textContent = 'Sesión para envíos: ' + names[current];
                return check();
            }).catch(function (error) {
                alertMessage(error.message, 'danger');
            }).finally(function () { busy = false; buttons(); });
        }

        select.addEventListener('change', function () { stopPolling(); current = select.value; buttons(); check(); });
        document.getElementById('btnRefreshSession').addEventListener('click', load);
        document.getElementById('btnStartSession').addEventListener('click', function () { action('start'); });
        document.getElementById('btnStopSession').addEventListener('click', function () { action('stop'); });
        document.getElementById('btnSetActiveSession').addEventListener('click', function () { action('active'); });
        document.getElementById('createSessionForm').addEventListener('submit', function (event) {
            event.preventDefault();
            if (busy || !this.reportValidity()) return;
            busy = true;
            buttons();
            document.getElementById('createSessionError').textContent = '';
            request(root.dataset.create, { name: document.getElementById('newSessionName').value.trim() }).then(function (data) {
                $('#modalCreateSession').modal('hide');
                alertMessage(data.message, 'success');
                return load();
            }).catch(function (error) {
                document.getElementById('createSessionError').textContent = error.message + ' Si la sesión se creó, cierra este diálogo y pulsa Actualizar para continuar.';
            }).finally(function () { busy = false; buttons(); });
        });
        window.addEventListener('beforeunload', stopPolling);
        load();
    });
}());
