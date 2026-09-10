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

        return view('whatsapp.chat', compact('conversaciones'));
    }

    /**
     * Obtener productos en bloques paginados por AJAX para el modal de WhatsApp
     */
    public function getProductsAjax(Request $request)
    {
        try {
            $search = trim((string) $request->input('search', ''));
            $page = (int) $request->input('page', 1);
            if ($page < 1) $page = 1;
            $limit = 15;
            $offset = ($page - 1) * $limit;

            $query = Producto::query();

            if (\Schema::hasColumn('productos', 'lActivo')) {
                $query->where('lActivo', 1);
            }

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('codigo_barras', 'like', '%' . $search . '%')
                      ->orWhere('descripcion', 'like', '%' . $search . '%');
                });
            }

            $total = (clone $query)->count();

            $productos = $query->orderBy('id', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get()
                ->map(function ($prod) {
                    return [
                        'id' => $prod->id,
                        'codigo_barras' => $prod->codigo_barras,
                        'descripcion' => $prod->descripcion,
                        'precio_venta' => number_format($prod->precio_venta, 2),
                        'existencia' => $prod->existencia,
                        'img_url' => $prod->img ? asset('img/productos/' . $prod->img) : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $productos,
                'page' => $page,
                'has_more' => ($offset + $productos->count()) < $total,
                'total' => $total,
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar productos: ' . $ex->getMessage()
            ], 500);
        }
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
                $mediaUrl = null;
                if ($msg->media_url) {
                    $mediaUrl = (strpos($msg->media_url, 'data:image') === 0 || filter_var($msg->media_url, FILTER_VALIDATE_URL)) 
                        ? $msg->media_url 
                        : asset($msg->media_url);
                }
                return [
                    'id' => $msg->id,
                    'direction' => $msg->direction,
                    'sender_name' => $msg->sender_name ?: ($msg->direction === 'outbound' ? 'Tienda' : 'Cliente'),
                    'body' => $msg->body,
                    'media_url' => $mediaUrl,
                    'type' => $msg->type,
                    'time' => $msg->created_at->format('H:i d/m/Y'),
                ];
            })
        ]);
    }

    /**
     * Enviar respuesta manual como agente humano (Texto e Imágenes en memoria sin guardar en disco)
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
                $filename = $file->getClientOriginalName();
                $mimeType = $file->getClientMimeType() ?: 'image/jpeg';

                // Convertir la imagen directamente a Base64 en memoria sin guardar en disco
                $rawBytes = file_get_contents($file->getRealPath());
                $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode($rawBytes);

                $mediaUrl = $base64Data;
                $msgType = 'image';

                // Enviar imagen por WhatsApp vía OpenWA
                $openWaService->sendImage($sessionId, $conversation->chat_id, $base64Data, $filename, $bodyText);

            } elseif ($request->filled('product_image_url')) {
                $rawUrl = $request->product_image_url;
                $filename = basename(parse_url($rawUrl, PHP_URL_PATH));

                $possiblePaths = [
                    public_path('img/productos/' . $filename),
                    public_path('img/' . $filename),
                    base_path('public/img/productos/' . $filename),
                ];

                $fullPath = null;
                foreach ($possiblePaths as $p) {
                    if (file_exists($p) && is_file($p)) {
                        $fullPath = $p;
                        break;
                    }
                }

                // Fallback sensible a mayúsculas/minúsculas para servidores Linux (Case-insensitive)
                if (!$fullPath) {
                    $dir = public_path('img/productos');
                    if (is_dir($dir)) {
                        $files = @scandir($dir);
                        if (is_array($files)) {
                            foreach ($files as $f) {
                                if (strtolower($f) === strtolower($filename)) {
                                    $fullPath = $dir . '/' . $f;
                                    $filename = $f;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($fullPath && is_file($fullPath)) {
                    $mediaUrl = 'img/productos/' . $filename;
                    $msgType = 'image';

                    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                    $mimeType = 'image/jpeg';
                    if (function_exists('mime_content_type')) {
                        $detectedMime = @mime_content_type($fullPath);
                        if ($detectedMime && strpos($detectedMime, 'image/') === 0) {
                            $mimeType = $detectedMime;
                        }
                    }
                    if ($mimeType === 'image/jpeg' && in_array($ext, ['png', 'webp', 'gif', 'svg'])) {
                        $mimeType = 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
                    }

                    $base64Data = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($fullPath));

                    $openWaService->sendImage($sessionId, $conversation->chat_id, $base64Data, $filename, $bodyText);
                } else {
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

            $finalMediaUrl = null;
            if ($message->media_url) {
                $finalMediaUrl = (strpos($message->media_url, 'data:image') === 0 || filter_var($message->media_url, FILTER_VALIDATE_URL)) 
                    ? $message->media_url 
                    : asset($message->media_url);
            }

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'direction' => $message->direction,
                    'sender_name' => $message->sender_name,
                    'body' => $message->body,
                    'media_url' => $finalMediaUrl,
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
