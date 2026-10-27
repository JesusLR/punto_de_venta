<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Cliente;
use App\WhatsAppConversation;
use App\Services\OpenWaService;
use App\Services\WhatsAppBotService;
use Mockery;

class WhatsAppBotTest extends TestCase
{
    public function test_whatsapp_bot_service_processes_catalogo_keyword()
    {
        $mockOpenWa = Mockery::mock(OpenWaService::class);
        $mockOpenWa->shouldReceive('sendText')->once()->andReturnTrue();

        $botService = new WhatsAppBotService($mockOpenWa);

        $payload = [
            'chatId' => '5219991234567@c.us',
            'body' => 'CATALOGO',
            'sender' => [
                'name' => 'Cliente Test'
            ]
        ];

        $processed = $botService->processIncomingMessage($payload);

        $this->assertTrue($processed);

        // Verificar que el cliente fue creado
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Cliente Test',
            'telefono' => '9991234567'
        ]);

        // Verificar registro de conversación
        $this->assertDatabaseHas('whatsapp_conversations', [
            'chat_id' => '5219991234567@c.us',
            'last_keyword' => 'CATALOGO'
        ]);
    }

    public function test_whatsapp_bot_service_processes_oro_keyword()
    {
        $mockOpenWa = Mockery::mock(OpenWaService::class);
        $mockOpenWa->shouldReceive('sendText')->once()->andReturnTrue();

        $botService = new WhatsAppBotService($mockOpenWa);

        $payload = [
            'chatId' => '5219997654321@c.us',
            'body' => 'ORO',
            'sender' => [
                'name' => 'Cliente Oro'
            ]
        ];

        $processed = $botService->processIncomingMessage($payload);

        $this->assertTrue($processed);

        $this->assertDatabaseHas('whatsapp_conversations', [
            'chat_id' => '5219997654321@c.us',
            'last_keyword' => 'ORO'
        ]);
    }
}
