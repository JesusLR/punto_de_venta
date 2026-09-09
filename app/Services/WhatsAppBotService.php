<?php

namespace App\Services;

use App\Cliente;
use App\Producto;
use App\Categorias;
use App\Apartado;
use App\PrecioMateriales;
use App\HomepageSetting;
use App\WhatsAppConversation;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    protected $openWaService;

    public function __construct(OpenWaService $openWaService)
    {
        $this->openWaService = $openWaService;
    }

    /**
     * Procesar un mensaje entrante de WhatsApp
     *
     * @param array $payload
     * @return bool
     */
    public function processIncomingMessage(array $payload)
    {
        try {
            // Extraer información relevante del payload de OpenWA
            $chatId = $payload['chatId'] ?? $payload['from'] ?? null;
            $body = trim($payload['body'] ?? $payload['text'] ?? $payload['content'] ?? '');
            $senderName = $payload['sender']['pushname'] ?? $payload['sender']['name'] ?? 'Cliente WhatsApp';

            if (empty($chatId) || empty($body)) {
                Log::warning('WhatsApp Webhook: Payload recibido sin chatId o body válidos', $payload);
                return false;
            }

            // Ignorar mensajes de grupos si es necesario
            if (strpos($chatId, '@g.us') !== false) {
                return false;
            }

            // Verificar si el bot está habilitado globalmente
            $botEnabled = HomepageSetting::getValue('wa_bot_enabled', '1');
            if ($botEnabled !== '1') {
                Log::info("WhatsApp Bot desactivado globalmente. Omitiendo respuesta a {$chatId}");
                return false;
            }

            // Sanitizar teléfono (ejemplo: 5211234567890@c.us -> 1234567890)
            $phoneDigits = preg_replace('/[^0-9]/', '', str_replace('@c.us', '', $chatId));
            $phoneShort = strlen($phoneDigits) > 10 ? substr($phoneDigits, -10) : $phoneDigits;

            // 1. Registro autómata de Cliente / Lead si no existe
            $cliente = Cliente::where('telefono', 'LIKE', "%{$phoneShort}%")->first();

            if (!$cliente) {
                $cliente = Cliente::create([
                    'nombre' => $senderName ?: 'Lead WhatsApp',
                    'telefono' => $phoneShort ?: $chatId,
                    'observaciones' => 'Registrado automáticamente desde chatbot de WhatsApp'
                ]);
                Log::info("Nuevo cliente registrado automáticamente desde WhatsApp: {$cliente->nombre} ({$cliente->telefono})");
            }

            // 2. Obtener o crear conversación activa
            $conversation = WhatsAppConversation::firstOrCreate(
                ['chat_id' => $chatId],
                [
                    'phone' => $phoneShort,
                    'cliente_id' => $cliente->id,
                    'step' => 'start',
                    'last_interaction_at' => now()
                ]
            );

            // Actualizar referencia de cliente si no estaba asociada
            if (!$conversation->cliente_id) {
                $conversation->cliente_id = $cliente->id;
            }

            // Si un agente humano ha tomado el control, no responder automáticamente a menos que escriba RESET
            if ($conversation->step === 'agent_active' && strtoupper($body) !== 'RESET' && strtoupper($body) !== 'MENU') {
                $conversation->update(['last_interaction_at' => now()]);
                return true;
            }

            // 3. Evaluar palabras clave y estado
            $response = $this->generateResponse($body, $conversation, $cliente);

            // 4. Enviar respuesta vía OpenWA
            $sessionId = config('services.openwa.session_id') ?: '581655e7-d546-4e9c-88da-f1f8843bc8f6';
            if (!empty($response['text'])) {
                $this->openWaService->sendText($sessionId, $chatId, $response['text']);
            }

            // Si hay un documento o PDF adjunto que enviar
            if (!empty($response['document'])) {
                $this->openWaService->sendDocument(
                    $sessionId,
                    $chatId,
                    $response['document']['base64'],
                    $response['document']['mimetype'],
                    $response['document']['filename']
                );
            }

            // Actualizar estado de la conversación
            $conversation->update([
                'step' => $response['next_step'] ?? $conversation->step,
                'last_keyword' => $response['keyword'] ?? $conversation->last_keyword,
                'last_interaction_at' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error procesando mensaje en WhatsAppBotService: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Determinar la respuesta según palabras clave y contexto
     */
    protected function generateResponse(string $body, WhatsAppConversation $conversation, Cliente $cliente): array
    {
        $cleanText = strtoupper(trim($body));

        // Detectar si proviene de enlaces con texto predefinido o palabras clave exactas
        if (strpos($cleanText, 'CATALOGO') !== false || strpos($cleanText, 'CATÁLOGO') !== false || $cleanText === '1') {
            return $this->getMenuCatalogoResponse();
        }

        if (strpos($cleanText, 'ORO') !== false || strpos($cleanText, 'MATERIALES') !== false || strpos($cleanText, 'PRECIO') !== false || $cleanText === '2') {
            return $this->getMenuOroResponse();
        }

        if (strpos($cleanText, 'APARTADO') !== false || strpos($cleanText, 'ABONO') !== false || $cleanText === '3') {
            return $this->getMenuApartadoResponse($cliente, $cleanText);
        }

        if (strpos($cleanText, 'AGENTE') !== false || strpos($cleanText, 'HUMANO') !== false || strpos($cleanText, 'ASESOR') !== false || $cleanText === '4') {
            return [
                'text' => "👩‍💻 *Atención Personalizada*\n\nHemo notificado a un asesor para atenderte personalmente. En breve responderemos tus dudas.\n\n_Para volver al menú automático escribe *MENU*._",
                'next_step' => 'agent_active',
                'keyword' => 'AGENTE'
            ];
        }

        // Si la conversación está esperando el folio de apartado
        if ($conversation->step === 'waiting_folio' && is_numeric($cleanText)) {
            return $this->getApartadoDetalleResponse((int)$cleanText);
        }

        // Menú principal por defecto
        return $this->getMainMenuResponse($cliente);
    }

    /**
     * Menú Principal del Bot
     */
    protected function getMainMenuResponse(Cliente $cliente): array
    {
        $customWelcome = HomepageSetting::getValue('wa_welcome_text');
        $nombreCliente = explode(' ', trim($cliente->nombre))[0];

        $saludo = $customWelcome ?: "👋 ¡Hola, *{$nombreCliente}*! Bienvenido(a) a nuestro centro de atención automatizada.";

        $text = "{$saludo}\n\n" .
            "Por favor selecciona una opción respondiendo con el *número* o la *palabra clave*:\n\n" .
            "1️⃣ *CATÁLOGO* - Ver categorías y productos disponibles 🛍️\n" .
            "2️⃣ *ORO* - Consultar el precio actualizado del oro/materiales 💍\n" .
            "3️⃣ *APARTADOS* - Consultar el estado de tu apartado o abonos 📋\n" .
            "4️⃣ *AGENTE* - Solicitar ayuda con un asesor humano 👩‍💻\n\n" .
            "_Responde en cualquier momento con el número de opción deseada._";

        return [
            'text' => $text,
            'next_step' => 'menu',
            'keyword' => 'MENU'
        ];
    }

    /**
     * Respuesta del Catálogo de Productos
     */
    protected function getMenuCatalogoResponse(): array
    {
        $categorias = Categorias::take(8)->get();
        $productos = Producto::where('existencia', '>', 0)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $text = "🛍️ *CATÁLOGO DE PRODUCTOS DISPONIBLES*\n\n";

        if ($categorias->isNotEmpty()) {
            $text .= "📌 *Categorías Principales:*\n";
            foreach ($categorias as $cat) {
                $text .= "• " . ($cat->cNombreCategoria ?? $cat->nombre ?? 'Categoría') . "\n";
            }
            $text .= "\n";
        }

        if ($productos->isNotEmpty()) {
            $text .= "✨ *Productos Destacados en Inventario:*\n";
            foreach ($productos as $prod) {
                $precio = number_format($prod->precio_venta, 2);
                $text .= "▫️ *{$prod->descripcion}*\n   Precio: \${$precio} MXN (Stock: {$prod->existencia})\n";
            }
        } else {
            $text .= "Actualmente estamos actualizando nuestro inventario de productos.\n";
        }

        $text .= "\n_Escribe *AGENTE* para cotizar una pieza en específico o *MENU* para volver._";

        return [
            'text' => $text,
            'next_step' => 'catalogo',
            'keyword' => 'CATALOGO'
        ];
    }

    /**
     * Respuesta de Precios de Oro / Materiales
     */
    protected function getMenuOroResponse(): array
    {
        $precioOro = PrecioMateriales::join("materiales", "precios_materiales.id_material", "=", "materiales.id")
            ->where('materiales.lActivoConsulta', 1)
            ->where('materiales.cSimbolo', "XAU")
            ->orderByDesc('precios_materiales.created_at')
            ->first();

        $text = "💍 *PRECIO DEL ORO DEL DÍA*\n\n";

        if ($precioOro && !empty($precioOro->json)) {
            $data = json_decode($precioOro->json, true);
            $text .= " Cotizaciones por gramo en MXN:\n\n";

            if (isset($data['price_gram_10k'])) {
                $text .= "🔸 *Oro 10K:* \$" . number_format($data['price_gram_10k'], 2) . " MXN / gramo\n";
            }
            if (isset($data['price_gram_14k'])) {
                $text .= "🔸 *Oro 14K:* \$" . number_format($data['price_gram_14k'], 2) . " MXN / gramo\n";
            }
            if (isset($data['price_gram_24k'])) {
                $text .= "🔸 *Oro 24K:* \$" . number_format($data['price_gram_24k'], 2) . " MXN / gramo\n";
            }

            $text .= "\n*Última actualización:* " . $precioOro->created_at->format('d/m/Y H:i') . "\n";
        } else {
            $text .= "La cotización del día está en actualización. Por favor consulta con un asesor escribiendo *AGENTE*.\n";
        }

        $text .= "\n_Escribe *MENU* para volver al menú principal._";

        return [
            'text' => $text,
            'next_step' => 'oro',
            'keyword' => 'ORO'
        ];
    }

    /**
     * Respuesta de Apartados del Cliente
     */
    protected function getMenuApartadoResponse(Cliente $cliente, string $rawText): array
    {
        // Buscar apartados vinculados a este cliente
        $apartados = Apartado::where('id_cliente', $cliente->id)
            ->orderBy('id', 'desc')
            ->get();

        if ($apartados->isEmpty()) {
            return [
                'text' => "📋 *CONSULTA DE APARTADOS*\n\nNo encontramos apartados registrados con tu número telefónico ({$cliente->telefono}).\n\nSi cuentas con tu número de folio de apartado, por favor escríbelo a continuación (ej: *15*):",
                'next_step' => 'waiting_folio',
                'keyword' => 'APARTADO'
            ];
        }

        $text = "📋 *TUS APARTADOS REGISTRADOS*\n\n";
        foreach ($apartados as $ap) {
            $nombreAp = $ap->nombre_apartado ?: "Apartado #{$ap->id}";
            $total = number_format($ap->total, 2);
            $abonado = number_format($ap->total_abonado, 2);
            $saldo = number_format($ap->saldo, 2);

            $text .= "🔹 *{$nombreAp}* (Folio: #{$ap->id})\n";
            $text .= "   • Total: \${$total}\n";
            $text .= "   • Abonado: \${$abonado}\n";
            $text .= "   • Saldo Pendiente: *\${$saldo}*\n";
            $text .= "   • Estado: {$ap->estado}\n\n";
        }

        $text .= "_Para ver detalles de un folio escribe el número del apartado (ej: *{$apartados->first()->id}*) o escribe *MENU*._";

        return [
            'text' => $text,
            'next_step' => 'menu_apartados',
            'keyword' => 'APARTADO'
        ];
    }

    /**
     * Respuesta detallada de un folio de apartado específico
     */
    protected function getApartadoDetalleResponse(int $folio): array
    {
        $apartado = Apartado::with(['cliente', 'abonos', 'productos.producto'])->find($folio);

        if (!$apartado) {
            return [
                'text' => "❌ No se encontró ningún apartado con el Folio #{$folio}.\n\nPor favor verifica el número o escribe *MENU* para volver.",
                'next_step' => 'menu',
                'keyword' => 'APARTADO_FAIL'
            ];
        }

        $total = number_format($apartado->total, 2);
        $abonado = number_format($apartado->total_abonado, 2);
        $saldo = number_format($apartado->saldo, 2);

        $text = "📋 *DETALLE DEL APARTADO #{$apartado->id}*\n";
        $text .= "Nombre: " . ($apartado->nombre_apartado ?: 'Sin nombre') . "\n";
        $text .= "Cliente: " . ($apartado->cliente ? $apartado->cliente->nombre : 'N/A') . "\n\n";

        $text .= "💰 *Monto Total:* \${$total} MXN\n";
        $text .= "✅ *Total Abonado:* \${$abonado} MXN\n";
        $text .= "⚠️ *Saldo Restante:* \${$saldo} MXN\n";
        $text .= "📌 *Estado:* {$apartado->estado}\n\n";

        if ($apartado->abonos->isNotEmpty()) {
            $text .= "📜 *Últimos Abonos:*\n";
            foreach ($apartado->abonos->take(3) as $ab) {
                $fecha = $ab->fecha_abono ? \Carbon\Carbon::parse($ab->fecha_abono)->format('d/m/Y') : $ab->created_at->format('d/m/Y');
                $text .= "   • {$fecha}: \$" . number_format($ab->monto, 2) . " ({$ab->tipo_pago})\n";
            }
            $text .= "\n";
        }

        $text .= "_Escribe *MENU* para volver al menú principal._";

        return [
            'text' => $text,
            'next_step' => 'menu',
            'keyword' => 'APARTADO_DETALLE'
        ];
    }
}
