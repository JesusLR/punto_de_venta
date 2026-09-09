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
}
