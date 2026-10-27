<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $botService;

    public function __construct(WhatsAppBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Endpoint para recibir webhooks entrantes de OpenWA
     * POST /api/whatsapp/webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('WhatsApp Webhook recibido:', $payload);

        // OpenWA puede enviar estructuras envueltas en data o event
        if (isset($payload['data'])) {
            $payload = $payload['data'];
        }

        $processed = $this->botService->processIncomingMessage($payload);

        return response()->json([
            'success' => true,
            'processed' => $processed
        ], 200);
    }
}
