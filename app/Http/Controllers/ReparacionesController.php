<?php

namespace App\Http\Controllers;

use App\Producto;
use App\Reparacion;
use App\Services\ReparacionesService;
use Illuminate\Http\Request;

class ReparacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver_reparaciones')->only('index');
        $this->middleware('permission:manage_reparaciones')->only(['store', 'entregar', 'productos', 'cambiarNombre', 'cancelar']);
        $this->middleware(\App\Http\Middleware\EnsureRepairAccess::class);
    }

    public function index(Request $request)
    {
        $request->validate(['estado' => 'nullable|in:EN_REPARACION,ENTREGADO,CANCELADO', 'usuario_id' => 'nullable|integer|exists:users,id']);
        $estadoFiltro = $request->input('estado', 'EN_REPARACION');
        $puedeVerTodas = $request->user()->hasPermission('ver_todas_reparaciones');
        $usuarioFiltro = $puedeVerTodas ? $request->input('usuario_id') : $request->user()->id;
        $reparaciones = Reparacion::visiblePara($request->user())->with(['piezas', 'imagenes', 'usuario', 'receptor', 'cancelador'])
            ->when($usuarioFiltro, function ($query) use ($usuarioFiltro) { $query->where('id_usuario', $usuarioFiltro); })
            ->when($estadoFiltro, function ($query) use ($estadoFiltro) { $query->where('estado', $estadoFiltro); })
            ->orderByDesc('id')->paginate(15)->appends(['estado' => $estadoFiltro ?? '', 'usuario_id' => $usuarioFiltro ?? '']);
        if ($request->ajax()) return response()->json(['html' => view('reparaciones.grid', compact('reparaciones'))->render()]);
        $usuariosFiltro = $puedeVerTodas ? \App\User::orderBy('name')->get(['id', 'name']) : collect();
        return view('reparaciones.index', compact('reparaciones', 'estadoFiltro', 'puedeVerTodas', 'usuarioFiltro', 'usuariosFiltro'));
    }

    public function productos(Request $request)
    {
        $data = $request->validate(['q' => 'nullable|string|max:255']);
        $query = Producto::where('lActivo', 1)->where('existencia', '>=', 1);
        $busqueda = trim($data['q'] ?? '');
        if ($busqueda !== '') $query->where(function ($query) use ($busqueda) {
            $query->where('codigo_barras', 'like', '%'.$busqueda.'%')->orWhere('descripcion', 'like', '%'.$busqueda.'%');
        });
        return response()->json($query->orderBy('descripcion')->limit(30)->get(['id', 'codigo_barras', 'descripcion', 'existencia']));
    }

    public function store(Request $request, ReparacionesService $service)
    {
        $data = $request->validate([
            'nombre' => 'nullable|string|max:150',
            'solicitud_id' => 'required|uuid', 'total' => 'required|numeric|min:0|max:9999999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'observaciones' => 'nullable|string|max:3000', 'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|integer|distinct', 'productos.*.cantidad' => 'required|integer|min:1|max:9999999',
        ]);
        $reparacion = $service->crear($data, $request->user()->id);
        return response()->json(['lSuccess' => true, 'id' => $reparacion->id]);
    }

    public function entregar(Request $request, Reparacion $reparacion, ReparacionesService $service, \App\Services\ReparacionesExternasService $externas)
    {
        if ($reparacion->tipo === 'EXTERNA') {
            $precios = $request->validate([
                'precio_compra' => 'required|numeric|min:0|max:9999999999.99|regex:/^\d+(\.\d{1,2})?$/',
                'precio_venta' => 'required|numeric|min:0|max:9999999999.99|regex:/^\d+(\.\d{1,2})?$/',
            ]);
            $externas->entregar($reparacion->id, $precios, $request->user()->id);
        } else {
            $service->entregar($reparacion->id, $request->user()->id);
        }
        return response()->json(['lSuccess' => true]);
    }

    public function cambiarNombre(Request $request, Reparacion $reparacion)
    {
        $data = $request->validate(['nombre' => 'present|nullable|string|max:150']);
        \Illuminate\Support\Facades\DB::transaction(function () use ($reparacion, $data) {
            $actual = Reparacion::whereKey($reparacion->id)->lockForUpdate()->firstOrFail();
            $actual->update(['nombre' => $data['nombre']]);
            if ($actual->id_egreso) {
                \App\Egreso::whereKey($actual->id_egreso)->update(['concepto' => $actual->conceptoEgreso()]);
            }
            if ($actual->id_ingreso) \App\Ingreso::whereKey($actual->id_ingreso)->update(['concepto' => $actual->conceptoEgreso()]);
        });
        return response()->json(['lSuccess' => true]);
    }

    public function cancelar(Request $request, Reparacion $reparacion, ReparacionesService $service)
    {
        $service->cancelar($reparacion->id, $request->user()->id);
        return response()->json(['lSuccess' => true]);
    }
}
