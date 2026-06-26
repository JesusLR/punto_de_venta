<?php

namespace App\Http\Controllers;

use App\HomepageSetting;
use Illuminate\Http\Request;
use Exception;

class GeneralSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_general_settings');
    }

    /**
     * Mostrar la vista de configuración general.
     */
    public function index()
    {
        // Traer todos los settings como array key => value
        $settings = HomepageSetting::pluck('value', 'key')->toArray();

        return view('general_settings.index', compact('settings'));
    }

    /**
     * Guardar la configuración general.
     */
    public function update(Request $request)
    {
        $request->validate([
            'apartado_inactive_days' => 'required|integer|min:1|max:365',
        ]);

        try {
            HomepageSetting::setValue('apartado_inactive_days', $request->apartado_inactive_days);

            return redirect()->route('general.settings.index')
                ->with('mensaje', 'Configuración general guardada correctamente.');
        } catch (Exception $ex) {
            return redirect()->route('general.settings.index')
                ->with('error', 'Error al guardar la configuración: ' . $ex->getMessage());
        }
    }
}
