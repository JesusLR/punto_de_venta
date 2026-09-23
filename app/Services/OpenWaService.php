<?php

namespace App\Services;

use App\HomepageSetting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenWaService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $url = HomepageSetting::getValue('wa_api_url') ?: config('services.openwa.url', 'http://74.208.53.13:2785');
        $this->baseUrl = rtrim($url, '/');
        $this->apiKey = config('services.openwa.key', 'owa_k1_4c2631a0321cb61e1d266787912fe331eef088fd850cfff50326b63cad9d9585');
    }

    /**
     * Obtener el ID de sesión activo configurado
     */
    public function getActiveSessionId(): string
    {
        return HomepageSetting::getValue('wa_session_id') ?: config('services.openwa.session_id', '581655e7-d546-4e9c-88da-f1f8843bc8f6');
    }

    /**
     * Obtener la URL base configurada para la API de OpenWA
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->withoutVerifying();
    }

    public function sendText(string $sessionId, string $chatId, string $text): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-text", [
                'chatId' => $chatId,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en OpenWaService sendText: ' . $e->getMessage());
            return null;
        }
    }

    public function sendDocument(string $sessionId, string $chatId, string $base64, string $mimetype, string $filename): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-document", [
                'chatId' => $chatId,
                'base64' => $base64,
                'mimetype' => $mimetype,
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en OpenWaService sendDocument: ' . $e->getMessage());
            return null;
        }
    }

    public function sendImage(string $sessionId, string $chatId, string $base64OrUrl, string $filename = 'producto.jpg', string $caption = '', string $mimetype = 'image/jpeg'): ?Response
    {
        try {
            $base64Data = $base64OrUrl;
            
            // Extraer automáticamente el mimetype si viene en formato Data URL (data:image/png;base64,...)
            if (preg_match('/^data:(image\/[a-zA-Z0-9\+\-\.]+);base64,/', $base64Data, $matches)) {
                $mimetype = $matches[1];
            } else if (strpos($base64Data, 'data:image') === false && !filter_var($base64Data, FILTER_VALIDATE_URL)) {
                $base64Data = 'data:' . $mimetype . ';base64,' . $base64Data;
            }

            $payload = [
                'chatId' => $chatId,
                'base64' => $base64Data,
                'mimetype' => $mimetype,
                'filename' => $filename,
                'caption' => $caption,
            ];

            $res = $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-image", $payload);
            if ($res && $res->successful()) {
                return $res;
            }

            // Fallback a sendDocument con el mimetype detectado
            return $this->sendDocument($sessionId, $chatId, $base64Data, $mimetype, $filename);
        } catch (\Exception $e) {
            Log::error('Error en OpenWaService sendImage: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenes lista de todas las sesiones registradas en OpenWA
     */
    public function getSessions(): ?Response
    {
        try {
            return $this->client()->get("{$this->baseUrl}/api/sessions");
        } catch (\Exception $e) {
            Log::error('Error en OpenWaService getSessions: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener una sesión específica por ID
     */
    public function getSession(string $sessionId): ?Response
    {
        try {
            return $this->client()->get("{$this->baseUrl}/api/sessions/{$sessionId}");
        } catch (\Exception $e) {
            Log::error("Error en OpenWaService getSession ({$sessionId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crear una nueva sesión en OpenWA
     */
    public function createSession(string $name): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions", [
                'name' => $name,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en OpenWaService createSession: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Iniciar una sesión en OpenWA (inicia motor de WhatsApp)
     */
    public function startSession(string $sessionId): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/start");
        } catch (\Exception $e) {
            Log::error("Error en OpenWaService startSession ({$sessionId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Detener una sesión en OpenWA (desconecta el motor)
     */
    public function stopSession(string $sessionId): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/stop");
        } catch (\Exception $e) {
            Log::error("Error en OpenWaService stopSession ({$sessionId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar una sesión en OpenWA
     */
    public function deleteSession(string $sessionId): ?Response
    {
        try {
            return $this->client()->delete("{$this->baseUrl}/api/sessions/{$sessionId}");
        } catch (\Exception $e) {
            Log::error("Error en OpenWaService deleteSession ({$sessionId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener el código QR de vinculación para una sesión
     */
    public function getQRCode(string $sessionId): ?Response
    {
        try {
            return $this->client()->get("{$this->baseUrl}/api/sessions/{$sessionId}/qr");
        } catch (\Exception $e) {
            Log::error("Error en OpenWaService getQRCode ({$sessionId}): " . $e->getMessage());
            return null;
        }
    }
}