<?php

namespace App\Services;

use App\Exceptions\OpenWaException;
use App\HomepageSetting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenWaService
{
    public function getBaseUrl(): string
    {
        $url = rtrim((string) config('services.openwa.url'), '/');
        return preg_match('~/api$~', $url) ? $url : $url.'/api';
    }

    private function request(string $method, string $path = '', array $data = [], ?string $idempotency = null): Response
    {
        $key = config('services.openwa.system_key');
        if (!$key) {
            throw new OpenWaException('Falta configurar OPENWA_SYSTEM_KEY para punto_de_venta en la conexión de WhatsApp.', 503);
        }
        $client = Http::withToken($key)->acceptJson()->timeout(45)->withOptions(['connect_timeout' => 5, 'allow_redirects' => false]);
        if ($idempotency) $client = $client->withHeaders(['Idempotency-Key' => $idempotency]);
        try {
            $response = $client->send($method, $this->getBaseUrl().'/integrations/sessions'.$path, $data ? ['json' => $data] : []);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            if (substr($path, -10) === '/documents') {
                throw new OpenWaException('Se perdió la conexión durante el envío. Revisa el chat de WhatsApp antes de volver a enviarlo.', 503);
            }
            throw new OpenWaException('No se pudo conectar con WhatsApp. Revisa que OpenWA esté disponible.', 503);
        }
        if (!$response->successful()) {
            Log::warning('OpenWA rechazó una solicitud de integración.', [
                'method' => $method, 'path' => $path ?: '/', 'status' => $response->status(),
            ]);
            $messages = [
                401 => 'La credencial de WhatsApp no es válida para este sistema.',
                403 => 'La credencial de WhatsApp no tiene acceso a este sistema.',
                404 => 'La sesión no existe en este sistema. Actualiza las sesiones en Inicio de sesión.',
                400 => 'WhatsApp no aceptó los datos. Verifica el número y el documento.',
                422 => 'WhatsApp no aceptó los datos. Verifica el número y el documento.',
                409 => 'La sesión no está conectada o hay una operación en curso. Revisa Inicio de sesión.',
                413 => 'El PDF supera el tamaño permitido de 15 MB.',
                429 => 'Se alcanzó el límite de solicitudes. Espera un momento antes de intentar de nuevo.',
                500 => 'OpenWA reportó un error interno (HTTP 500). Revisa los registros del servicio.',
                502 => 'El servidor intermedio no pudo comunicarse con OpenWA (HTTP 502).',
                503 => 'OpenWA no está disponible o su configuración de integraciones no es válida (HTTP 503). Revisa INTEGRATION_SYSTEMS y reinicia el servicio.',
                504 => 'OpenWA tardó demasiado en responder (HTTP 504).',
            ];
            throw new OpenWaException($messages[$response->status()] ?? 'OpenWA no pudo completar la solicitud (HTTP '.$response->status().').', in_array($response->status(), [400, 404, 409, 413, 422, 429]) ? $response->status() : 503);
        }
        if ($response->status() !== 204 && !is_array($response->json())) {
            throw new OpenWaException('OpenWA devolvió una respuesta no válida.', 502);
        }
        return $response;
    }

    private function sessionPath(string $id): string
    {
        if (!Str::isUuid($id)) throw new OpenWaException('Selecciona una sesión válida de este sistema.', 422);
        return '/'.$id;
    }

    private function validateSession(array $session, ?string $expectedId = null): void
    {
        if (($session['sourceSystem'] ?? null) !== config('services.openwa.source_system')
            || !is_string($session['id'] ?? null) || !Str::isUuid($session['id'])
            || ($expectedId && $session['id'] !== $expectedId)) {
            throw new OpenWaException('OpenWA devolvió una sesión que no corresponde a este sistema.', 403);
        }
    }

    public function getSessions(): Response
    {
        $response = $this->request('GET');
        $data = $response->json();
        if (($data['sourceSystem'] ?? null) !== config('services.openwa.source_system') || !is_array($data['sessions'] ?? null)) {
            throw new OpenWaException('La conexión de WhatsApp no corresponde a punto_de_venta.', 403);
        }
        foreach ($data['sessions'] as $session) {
            if (!is_array($session)) throw new OpenWaException('OpenWA devolvió una sesión no válida.', 502);
            $this->validateSession($session);
        }
        return $response;
    }

    public function getActiveSessionId(?array $sessions = null): string
    {
        $sessions = $sessions ?? $this->getSessions()->json()['sessions'];
        $saved = HomepageSetting::getValue('wa_session_id') ?: config('services.openwa.session_id');
        foreach ($sessions as $session) {
            $this->validateSession($session);
            if ($session['id'] === $saved) return $saved;
        }
        foreach ($sessions as $session) {
            if (strtolower($session['status'] ?? '') === 'ready' && !empty($session['engineLoaded'])) return $session['id'];
        }
        return $sessions[0]['id'] ?? '';
    }

    public function getSession(string $id): Response
    {
        $response = $this->request('GET', $this->sessionPath($id));
        $this->validateSession($response->json(), $id);
        return $response;
    }

    public function createSession(string $name): Response
    {
        $response = $this->request('POST', '', ['name' => $name]);
        $this->validateSession($response->json());
        return $response;
    }

    public function startSession(string $id): Response
    {
        $response = $this->request('POST', $this->sessionPath($id).'/start');
        $this->validateSession($response->json(), $id);
        return $response;
    }

    public function stopSession(string $id): Response
    {
        $response = $this->request('POST', $this->sessionPath($id).'/logout');
        $this->validateSession($response->json(), $id);
        return $response;
    }

    public function deleteSession(string $id): Response
    {
        return $this->request('DELETE', $this->sessionPath($id));
    }

    public function getQRCode(string $id): Response
    {
        $response = $this->request('GET', $this->sessionPath($id).'/qr');
        $qr = $response->json()['qrCode'] ?? null;
        if ($qr !== null && (!is_string($qr) || strlen($qr) > 1000000 || !preg_match('~\Adata:image/png;base64,[A-Za-z0-9+/=]+\z~', $qr))) {
            throw new OpenWaException('OpenWA devolvió un código QR no válido.', 502);
        }
        return $response;
    }

    public function sendPdf(string $phone, string $content, string $filename, string $caption = ''): Response
    {
        $id = $this->getActiveSessionId();
        if (!$id) throw new OpenWaException('No hay una sesión de WhatsApp. Vincula una desde Inicio de sesión.', 409);
        return $this->sendDocument($id, $phone, base64_encode($content), 'application/pdf', $filename, $caption);
    }

    public function sendDocument(string $id, string $chatId, string $base64, string $mimetype, string $filename, string $caption = ''): Response
    {
        $session = $this->getSession($id)->json();
        if (strtolower($session['status'] ?? '') !== 'ready' || empty($session['engineLoaded'])) {
            throw new OpenWaException('La sesión de WhatsApp no está conectada. Vincúlala desde Inicio de sesión.', 409);
        }
        $phone = preg_replace('/\D/', '', preg_replace('/@.*$/', '', $chatId));
        if (strlen($phone) === 13 && strpos($phone, '521') === 0) $phone = '52'.substr($phone, 3);
        if (strlen($phone) === 10) $phone = '52'.$phone;
        $cleanBase64 = preg_replace('/^data:[^;]+;base64,/', '', $base64);
        $content = base64_decode($cleanBase64, true);
        if ($mimetype !== 'application/pdf' || $content === false || substr($content, 0, 5) !== '%PDF-') {
            throw new OpenWaException('Solo se pueden enviar documentos PDF válidos.', 422);
        }
        if (strlen($content) > 15 * 1024 * 1024) throw new OpenWaException('El PDF supera los 15 MB permitidos.', 413);
        if (!preg_match('/^[1-9][0-9]{7,14}$/D', $phone)) throw new OpenWaException('El teléfono de WhatsApp no es válido.', 422);
        $response = $this->request('POST', $this->sessionPath($id).'/documents', [
            'phone' => $phone, 'base64' => $cleanBase64, 'filename' => $filename, 'caption' => $caption,
        ], (string) Str::uuid());
        if (($response->json()['status'] ?? null) !== 'sent') {
            throw new OpenWaException('No se pudo confirmar el envío. Revisa el chat de WhatsApp antes de volver a enviar.', 502);
        }
        return $response;
    }

    public function sendText(string $sessionId, string $chatId, string $text): ?Response
    {
        throw new OpenWaException('El centro de mensajes y las respuestas automáticas están deshabilitados.', 409);
    }

    public function sendImage(string $sessionId, string $chatId, string $base64OrUrl, string $filename = 'producto.jpg', string $caption = '', string $mimetype = 'image/jpeg'): ?Response
    {
        throw new OpenWaException('El centro de mensajes está deshabilitado por el momento.', 409);
    }
}
