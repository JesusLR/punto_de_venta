<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class OpenWaService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.openwa.url', 'http://74.208.53.13:2785'), '/');
        $this->apiKey = config('services.openwa.key', 'owa_k1_4c2631a0321cb61e1d266787912fe331eef088fd850cfff50326b63cad9d9585');
    }

    private function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function sendText(string $sessionId, string $chatId, string $text): ?Response
    {
        try {
            return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-text", [
                'chatId' => $chatId,
                'text' => $text,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en OpenWaService sendText: ' . $e->getMessage());
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
            \Illuminate\Support\Facades\Log::error('Error en OpenWaService sendDocument: ' . $e->getMessage());
            return null;
        }
    }

    public function sendImage(string $sessionId, string $chatId, string $base64OrUrl, string $filename = 'producto.jpg', string $caption = ''): ?Response
    {
        try {
            $base64Data = $base64OrUrl;
            if (strpos($base64Data, 'data:image') === false && !filter_var($base64Data, FILTER_VALIDATE_URL)) {
                $base64Data = 'data:image/jpeg;base64,' . $base64Data;
            }

            $payload = [
                'chatId' => $chatId,
                'base64' => $base64Data,
                'mimetype' => 'image/jpeg',
                'filename' => $filename,
                'caption' => $caption,
            ];

            $res = $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-image", $payload);
            if ($res && $res->successful()) {
                return $res;
            }

            // Fallback a sendDocument
            return $this->sendDocument($sessionId, $chatId, $base64Data, 'image/jpeg', $filename);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en OpenWaService sendImage: ' . $e->getMessage());
            return null;
        }
    }
}