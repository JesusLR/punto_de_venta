<?php

namespace App\Http\Controllers;

use App\HomepageSetting;
use App\Categorias;
use App\Producto;
use App\WhatsAppConversation;
use Illuminate\Http\Request;
use Exception;

class WhatsAppSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vista de Generador de Enlaces de WhatsApp
     */
    public function indexLinks(Request $request)
    {
        $storePhone = HomepageSetting::getValue('wa_phone_number', '5219991629742');
        $categorias = Categorias::all();
        $productos = Producto::orderBy('id', 'desc')->take(20)->get();

        // Conversaciones recientes
        $conversaciones = WhatsAppConversation::with('cliente')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        return view('whatsapp.links', compact('storePhone', 'categorias', 'productos', 'conversaciones'));
    }

    /**
     * Vista de Configuración del Bot de WhatsApp
     */
    public function indexSettings(Request $request)
    {
        $settings = [
            'wa_phone_number' => HomepageSetting::getValue('wa_phone_number', '5219991629742'),
            'wa_bot_enabled' => HomepageSetting::getValue('wa_bot_enabled', '1'),
            'wa_welcome_text' => HomepageSetting::getValue('wa_welcome_text', ''),
        ];

        return view('whatsapp.settings', compact('settings'));
    }

    /**
     * Guardar Configuración del Bot
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'wa_phone_number' => 'nullable|string|max:20',
            'wa_bot_enabled' => 'required|in:0,1',
            'wa_welcome_text' => 'nullable|string|max:1000',
        ]);

        try {
            HomepageSetting::setValue('wa_phone_number', preg_replace('/[^0-9]/', '', $request->wa_phone_number));
            HomepageSetting::setValue('wa_bot_enabled', $request->wa_bot_enabled);
            HomepageSetting::setValue('wa_welcome_text', $request->wa_welcome_text);

            return redirect()->route('whatsapp.settings.index')
                ->with('mensaje', 'Configuración de WhatsApp guardada correctamente.');
        } catch (Exception $ex) {
            return redirect()->route('whatsapp.settings.index')
                ->with('error', 'Error al guardar la configuración: ' . $ex->getMessage());
        }
    }

    /**
     * Vista de Centro de Mensajes / Chat en Vivo
     */
    public function indexChat(Request $request)
    {
        $conversaciones = WhatsAppConversation::with(['cliente', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $productos = Producto::orderBy('descripcion', 'asc')->get();

        return view('whatsapp.chat', compact('conversaciones', 'productos'));
    }

    /**
     * Obtener lista de conversaciones (API AJAX)
     */
    public function getConversations(Request $request)
    {
        $conversaciones = WhatsAppConversation::with(['cliente', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conv) {
                return [
                    'id' => $conv->id,
                    'chat_id' => $conv->chat_id,
                    'phone' => $conv->phone,
                    'nombre' => $conv->cliente ? $conv->cliente->nombre : 'Lead Anónimo',
                    'step' => $conv->step,
                    'last_keyword' => $conv->last_keyword,
                    'last_message' => $conv->lastMessage ? $conv->lastMessage->body : 'Sin mensajes',
                    'updated_at' => $conv->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'conversations' => $conversaciones
        ]);
    }

    /**
     * Obtener mensajes de una conversación específica (API AJAX)
     */
    public function getMessages(Request $request, $id)
    {
        $conversation = WhatsAppConversation::with(['cliente', 'messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'chat_id' => $conversation->chat_id,
                'phone' => $conversation->phone,
                'nombre' => $conversation->cliente ? $conversation->cliente->nombre : 'Lead Anónimo',
                'step' => $conversation->step,
            ],
            'messages' => $conversation->messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'direction' => $msg->direction,
                    'sender_name' => $msg->sender_name ?: ($msg->direction === 'outbound' ? 'Tienda' : 'Cliente'),
                    'body' => $msg->body,
                    'media_url' => $msg->media_url ? asset($msg->media_url) : null,
                    'type' => $msg->type,
                    'time' => $msg->created_at->format('H:i d/m/Y'),
                ];
            })
        ]);
    }

    /**
     * Enviar respuesta manual como agente humano (Texto e Imágenes)
     */
    public function sendMessage(Request $request, \App\Services\OpenWaService $openWaService)
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
            'body' => 'nullable|string|max:2000',
            'image' => 'nullable|image|max:10240', // max 10MB
            'product_image_url' => 'nullable|string',
        ]);

        try {
            $conversation = WhatsAppConversation::findOrFail($request->conversation_id);
            $sessionId = config('services.openwa.session_id') ?: '581655e7-d546-4e9c-88da-f1f8843bc8f6';
            $bodyText = $request->body ?: '';
            $mediaUrl = null;
            $msgType = 'text';

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());

                $destinationPath = public_path('uploads/whatsapp');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $mediaUrl = 'uploads/whatsapp/' . $filename;
                $msgType = 'image';

                // Convertir a Data URL para envío por OpenWA
                $fullPath = $destinationPath . '/' . $filename;
                $mimeType = function_exists('mime_content_type') ? mime_content_type($fullPath) : 'image/jpeg';
                $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fullPath));

                // Enviar imagen por WhatsApp vía OpenWA
                $openWaService->sendImage($sessionId, $conversation->chat_id, $base64Data, $filename, $bodyText);
            } elseif ($request->filled('product_image_url')) {
                $pathOnly = parse_url($request->product_image_url, PHP_URL_PATH);
                $relPath = ltrim($pathOnly, '/');
                $fullPath = public_path($relPath);

                if (file_exists($fullPath)) {
                    $mediaUrl = $relPath;
                    $msgType = 'image';
                    $filename = basename($fullPath);
                    $mimeType = function_exists('mime_content_type') ? mime_content_type($fullPath) : 'image/jpeg';
                    $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fullPath));

                    $openWaService->sendImage($sessionId, $conversation->chat_id, $base64Data, $filename, $bodyText);
                } else {
                    // Si no existe físicamente en servidor, enviar texto
                    $openWaService->sendText($sessionId, $conversation->chat_id, $bodyText);
                }
            } else {
                if (empty($bodyText)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Por favor escribe un mensaje o selecciona una imagen.'
                    ], 422);
                }
                // Enviar texto por OpenWA
                $openWaService->sendText($sessionId, $conversation->chat_id, $bodyText);
            }

            // Registrar mensaje saliente
            $message = \App\WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'chat_id' => $conversation->chat_id,
                'direction' => 'outbound',
                'sender_name' => auth()->user() ? auth()->user()->name : 'Asesor Joyería Colibrí',
                'body' => $bodyText,
                'media_url' => $mediaUrl,
                'type' => $msgType,
            ]);

            // Pausar el bot y poner la conversación en modo 'agent_active'
            $conversation->update([
                'step' => 'agent_active',
                'last_interaction_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'direction' => $message->direction,
                    'sender_name' => $message->sender_name,
                    'body' => $message->body,
                    'media_url' => $message->media_url ? asset($message->media_url) : null,
                    'type' => $message->type,
                    'time' => $message->created_at->format('H:i d/m/Y'),
                ]
            ]);

        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar mensaje: ' . $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Alternar estado del bot para una conversación (Pausar / Reactivar)
     */
    public function toggleBotStatus(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:whatsapp_conversations,id',
            'status' => 'required|in:active,paused',
        ]);

        $conversation = WhatsAppConversation::findOrFail($request->conversation_id);
        $newStep = $request->status === 'active' ? 'menu' : 'agent_active';
        $conversation->update(['step' => $newStep]);

        return response()->json([
            'success' => true,
            'new_step' => $newStep
        ]);
    }

    /**
     * Eliminar una conversación y todo su historial de mensajes
     */
    public function deleteConversation(Request $request, $id)
    {
        try {
            $conversation = WhatsAppConversation::findOrFail($id);
            $conversation->messages()->delete();
            $conversation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Conversación eliminada correctamente.'
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la conversación: ' . $ex->getMessage()
            ], 500);
        }
    }
}
