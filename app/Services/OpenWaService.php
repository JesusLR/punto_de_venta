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
        $this->baseUrl = rtrim('http://74.208.53.13:2785', '/'); //Guardar en variable de configuracion
        $this->apiKey = "owa_k1_4c2631a0321cb61e1d266787912fe331eef088fd850cfff50326b63cad9d9585"; //Guardar en variable de configuracion
    }

    private function client()
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    public function sendText(string $sessionId, string $chatId, string $text): Response
    {
        // dd("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-text",$this->baseUrl,$this->apiKey, $sessionId,  $chatId,  $text);
         return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-text", [
            'chatId' => $chatId,
            'text' => $text,
        ]);
        // dd('hola',$a);
    }

    public function sendDocument(string $sessionId, string $chatId, string $base64, string $mimetype, string $filename): Response
    {
        return $this->client()->post("{$this->baseUrl}/api/sessions/{$sessionId}/messages/send-document", [
            'chatId' => $chatId,
            'base64' => $base64,
            'mimetype' => $mimetype,
            'filename' => $filename,
        ]);
    }
}