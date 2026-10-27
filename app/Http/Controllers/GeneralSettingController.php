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
        $egresosAutomaticos = \App\EgresoAutomatico::orderBy('dia_mes')->get();

        return view('general_settings.index', compact('settings', 'egresosAutomaticos'));
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

    /**
     * Guardar un nuevo egreso automático.
     */
    public function storeEgresoAutomatico(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'concepto' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'frecuencia' => 'required|string|in:MENSUAL,SEMANAL',
            'dia_mes' => 'required_if:frecuencia,MENSUAL|nullable|integer|min:1|max:31',
            'dia_semana' => 'required_if:frecuencia,SEMANAL|nullable|integer|min:1|max:7',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => $validator->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            \App\EgresoAutomatico::create([
                'concepto' => strtoupper(trim($request->concepto)),
                'monto' => $request->monto,
                'frecuencia' => $request->frecuencia,
                'dia_mes' => $request->frecuencia === 'MENSUAL' ? $request->dia_mes : null,
                'dia_semana' => $request->frecuencia === 'SEMANAL' ? $request->dia_semana : null,
                'observaciones' => $request->observaciones,
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => true,
                    'cMensaje' => 'Egreso automático guardado correctamente.',
                ]);
            }

            return redirect()->route('general.settings.index')
                ->with('mensaje', 'Egreso automático guardado correctamente.');
        } catch (Exception $ex) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => 'Error al guardar: ' . $ex->getMessage(),
                ], 500);
            }
            return redirect()->route('general.settings.index')
                ->with('error', 'Error al guardar el egreso automático: ' . $ex->getMessage());
        }
    }

    /**
     * Eliminar un egreso automático.
     */
    public function destroyEgresoAutomatico(Request $request, $id)
    {
        try {
            $egreso = \App\EgresoAutomatico::findOrFail($id);
            $egreso->delete();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => true,
                    'cMensaje' => 'Egreso automático eliminado correctamente.',
                ]);
            }

            return redirect()->route('general.settings.index')
                ->with('mensaje', 'Egreso automático eliminado correctamente.');
        } catch (Exception $ex) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => 'Error al eliminar: ' . $ex->getMessage(),
                ], 500);
            }
            return redirect()->route('general.settings.index')
                ->with('error', 'Error al eliminar el egreso automático: ' . $ex->getMessage());
        }
    }
}
