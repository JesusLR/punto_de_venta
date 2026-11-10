<?php

namespace App\Http\Controllers;

use App\Reparacion;
use App\ReparacionImagen;
use App\Services\ReparacionesExternasService;
use App\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReparacionesExternasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_reparaciones')->only(['store', 'clientes', 'edit', 'update']);
        $this->middleware('permission:ver_reparaciones')->only(['imagen', 'pdf']);
        $this->middleware(\App\Http\Middleware\EnsureRepairAccess::class);
    }

    public function clientes(Request $request)
    {
        $data = $request->validate(['q' => 'nullable|string|max:255']);
        return response()->json(Cliente::where('nombre', 'like', '%'.trim($data['q'] ?? '').'%')->orderBy('nombre')->limit(30)->get(['id', 'nombre', 'telefono']));
    }

    public function store(Request $request, ReparacionesExternasService $service)
    {
        $request->merge(['contacto_telefono' => $request->filled('contacto_telefono') ? preg_replace('/\D/', '', $request->contacto_telefono) : null]);
        $data = $request->validate([
            'solicitud_id' => 'required|uuid', 'nombre' => 'nullable|string|max:150',
            'contacto_origen' => 'required|in:catalogo,manual',
            'contacto_cliente_id' => 'required_if:contacto_origen,catalogo|nullable|integer|exists:clientes,id',
            'contacto_nombre' => 'required_if:contacto_origen,manual|nullable|string|max:255',
            'contacto_telefono' => 'required_if:contacto_origen,manual|nullable|string|regex:/^[1-9][0-9]{7,14}$/D',
            'observaciones' => 'required|string|max:5000',
        ] + $this->reglasImagenes());
        $reparacion = $service->crear($data, $this->archivos($request), $request->user()->id);
        return response()->json(['lSuccess' => true, 'id' => $reparacion->id]);
    }

    private function reglasImagenes()
    {
        $rules = [];
        foreach (['imagenes', 'imagenes_antes', 'imagenes_despues'] as $campo) {
            $rules[$campo] = 'nullable|array|max:10';
            $rules[$campo.'.*'] = 'required|image|mimes:jpeg,jpg,png|max:2048|dimensions:max_width=4000,max_height=4000';
        }
        return $rules;
    }

    private function archivos(Request $request)
    {
        return ['ANTES' => array_merge($request->file('imagenes', []), $request->file('imagenes_antes', [])), 'DESPUES' => $request->file('imagenes_despues', [])];
    }

    public function edit(Reparacion $reparacion)
    {
        abort_unless($reparacion->tipo === 'EXTERNA', 404);
        return response()->json([
            'nombre' => $reparacion->nombre, 'observaciones' => $reparacion->observaciones,
            'contacto_nombre' => $reparacion->contacto_nombre, 'contacto_telefono' => $reparacion->contacto_telefono,
            'url' => route('reparaciones.externas.update', $reparacion),
            'imagenes' => $reparacion->imagenes()->orderBy('id')->get()->map(function ($imagen) use ($reparacion) {
                return ['id' => $imagen->id, 'etapa' => $imagen->etapa, 'url' => route('reparaciones.imagen', [$reparacion, $imagen])];
            }),
        ]);
    }

    public function update(Request $request, Reparacion $reparacion, ReparacionesExternasService $service)
    {
        abort_unless($reparacion->tipo === 'EXTERNA', 404);
        $data = $request->validate([
            'nombre' => 'nullable|string|max:150', 'observaciones' => 'required|string|max:5000',
            'eliminar_imagenes' => 'nullable|array', 'eliminar_imagenes.*' => 'required|integer|distinct',
        ] + $this->reglasImagenes());
        $service->actualizar($reparacion->id, $data, $this->archivos($request));
        return response()->json(['lSuccess' => true]);
    }

    public function imagen(Reparacion $reparacion, ReparacionImagen $imagen)
    {
        abort_unless((int) $imagen->reparacion_id === (int) $reparacion->id, 404);
        abort_unless(Storage::disk('local')->exists($imagen->ruta), 404);
        return response()->file(Storage::disk('local')->path($imagen->ruta), ['X-Content-Type-Options' => 'nosniff']);
    }

    public function pdf(Request $request, Reparacion $reparacion)
    {
        abort_unless($reparacion->tipo === 'EXTERNA', 404);
        $imagenes = collect();
        if ($request->boolean('imagenes', true)) {
            $reparacion->load('imagenes');
            $imagenes = $reparacion->imagenes->map(function ($imagen) {
            return Storage::disk('local')->exists($imagen->ruta) ? ['ruta' => str_replace('\\', '/', Storage::disk('local')->path($imagen->ruta)), 'etapa' => $imagen->etapa] : null;
            })->filter()->values();
        }
        $pdf = Pdf::loadView('reparaciones.pdf', compact('reparacion', 'imagenes'))->setPaper('letter', 'portrait');
        $nombre = 'Reparacion externa - '.$reparacion->id.' - '.Str::limit(Str::slug($reparacion->contacto_nombre), 60, '').'.pdf';
        return $request->boolean('descargar') ? $pdf->download($nombre) : $pdf->stream($nombre);
    }
}
