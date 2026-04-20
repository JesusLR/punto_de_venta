<?php

namespace App\Http\Controllers;

use App\Egreso;
use App\Exports\FinanzasExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FinanzasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->fecha_fin)->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            $fechaFin = $fechaInicio->copy()->endOfDay();
        }

        $egresos = Egreso::with('usuario')
            ->whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'egresos_page')
            ->appends($request->except('egresos_page'));

        $totalEgresos = (float) Egreso::whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
            ->sum('monto');

        $ventasIngresos = DB::table('ventas')
            ->join('productos_vendidos', 'productos_vendidos.id_venta', '=', 'ventas.id')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.id_cliente')
            ->whereBetween('ventas.created_at', [$fechaInicio->toDateTimeString(), $fechaFin->toDateTimeString()])
            ->where(function ($query) {
                $query->whereNull('ventas.tipo_pago')
                    ->orWhereIn('ventas.tipo_pago', ['EFECTIVO', 'MERCADO_PAGO']);
            })
            ->groupBy('ventas.id', 'ventas.created_at', 'ventas.tipo_pago', 'clientes.nombre')
            ->select(
                'ventas.id',
                'ventas.created_at',
                'ventas.tipo_pago',
                'ventas.cNombreVenta',
                'clientes.nombre as cliente_nombre',
                DB::raw('SUM(productos_vendidos.cantidad * productos_vendidos.precio) as monto')
            )
            ->orderByDesc('ventas.created_at')
            ->get();

        $abonosIngresos = DB::table('apartado_abonos')
            ->leftJoin('apartados', 'apartados.id', '=', 'apartado_abonos.id_apartado')
            ->leftJoin('clientes', 'clientes.id', '=', 'apartados.id_cliente')
            ->whereBetween(
                DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at)'),
                [$fechaInicio->toDateTimeString(), $fechaFin->toDateTimeString()]
            )
            ->select(
                'apartado_abonos.id',
                'apartado_abonos.monto',
                'apartado_abonos.tipo_pago',
                'apartados.nombre_apartado',
                'apartados.id as apartado_id',
                'clientes.nombre as cliente_nombre',
                DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at) as fecha_movimiento')
            )
            ->orderByDesc('fecha_movimiento')
            ->get();

        $totalVentas = (float) $ventasIngresos->sum('monto');
        $totalAbonos = (float) $abonosIngresos->sum('monto');
        $totalIngresos = $totalVentas + $totalAbonos;

        $movimientosIngresos = collect();

        foreach ($ventasIngresos as $venta) {
            $movimientosIngresos->push([
                'fecha' => Carbon::parse($venta->created_at),
                'tipo' => 'VENTA',
                'metodo' => $venta->tipo_pago ?: 'EFECTIVO',
                'referencia' => strlen($venta->cNombreVenta) == 0 ? 'Comprobante de venta folio #' . $venta->id : $venta->cNombreVenta,
                'detalle' => 'Cliente: ' . ($venta->cliente_nombre ?: 'N/A'),
                'monto' => (float) $venta->monto,
            ]);
        }

        foreach ($abonosIngresos as $abono) {
            $movimientosIngresos->push([
                'fecha' => Carbon::parse($abono->fecha_movimiento),
                'tipo' => 'ABONO',
                'metodo' => $abono->tipo_pago ?: 'EFECTIVO',
                'referencia' => strlen($abono->nombre_apartado) == 0 ? 'Comprobante de apartado folio #' . $abono->apartado_id : $abono->nombre_apartado,
                'detalle' => 'Cliente: ' . ($abono->cliente_nombre ?: 'N/A'),
                'monto' => (float) $abono->monto,
            ]);
        }

        $movimientosIngresos = $movimientosIngresos
            ->sortByDesc('fecha')
            ->values();

        $totalIngresoEfectivo = (float) $movimientosIngresos
            ->where('metodo', 'EFECTIVO')
            ->sum('monto');

        $totalIngresoMercadoPago = (float) $movimientosIngresos
            ->where('metodo', 'MERCADO_PAGO')
            ->sum('monto');

        $ingresosPage = Paginator::resolveCurrentPage('ingresos_page');
        $ingresosPorPagina = 10;
        $ingresosTotales = $movimientosIngresos->count();
        $movimientosIngresos = new LengthAwarePaginator(
            $movimientosIngresos->slice(($ingresosPage - 1) * $ingresosPorPagina, $ingresosPorPagina)->values(),
            $ingresosTotales,
            $ingresosPorPagina,
            $ingresosPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'ingresos_page',
            ]
        );

        $movimientosIngresos->appends($request->except('ingresos_page'));

        $balanceNeto = $totalIngresos - $totalEgresos;

        return view('finanzas.finanzas_index', [
            'fechaInicio' => $fechaInicio->toDateString(),
            'fechaFin' => $fechaFin->toDateString(),
            'fechaHoy' => Carbon::now()->toDateString(),
            'egresos' => $egresos,
            'movimientosIngresos' => $movimientosIngresos,
            'totalIngresos' => $totalIngresos,
            'totalIngresoEfectivo' => $totalIngresoEfectivo,
            'totalIngresoMercadoPago' => $totalIngresoMercadoPago,
            'totalEgresos' => $totalEgresos,
            'balanceNeto' => $balanceNeto,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'concepto' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        Egreso::create([
            'id_usuario' => auth()->id(),
            'concepto' => strtoupper(trim($request->concepto)),
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'observaciones' => $request->observaciones,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Egreso guardado con éxito',
            ]);
        }

        return redirect()->route('finanzas.index', [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ])->with([
            'mensaje' => 'Egreso guardado con éxito',
            'tipo' => 'success',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $egreso = Egreso::findOrFail($id);
        $egreso->delete();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Egreso eliminado con éxito',
            ]);
        }

        return redirect()->route('finanzas.index', [
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ])->with([
            'mensaje' => 'Egreso eliminado con éxito',
            'tipo' => 'success',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::parse($request->fecha_inicio)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::parse($request->fecha_fin)->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            $fechaFin = $fechaInicio->copy()->endOfDay();
        }

        $egresos = Egreso::with('usuario')
            ->whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        $totalEgresos = (float) Egreso::whereBetween('fecha', [$fechaInicio->toDateString(), $fechaFin->toDateString()])
            ->sum('monto');

        $ventasIngresos = DB::table('ventas')
            ->join('productos_vendidos', 'productos_vendidos.id_venta', '=', 'ventas.id')
            ->leftJoin('clientes', 'clientes.id', '=', 'ventas.id_cliente')
            ->whereBetween('ventas.created_at', [$fechaInicio->toDateTimeString(), $fechaFin->toDateTimeString()])
            ->where(function ($query) {
                $query->whereNull('ventas.tipo_pago')
                    ->orWhereIn('ventas.tipo_pago', ['EFECTIVO', 'MERCADO_PAGO']);
            })
            ->groupBy('ventas.id', 'ventas.created_at', 'ventas.tipo_pago', 'clientes.nombre')
            ->select(
                'ventas.id',
                'ventas.created_at',
                'ventas.tipo_pago',
                'ventas.cNombreVenta',
                'clientes.nombre as cliente_nombre',
                DB::raw('SUM(productos_vendidos.cantidad * productos_vendidos.precio) as monto')
            )
            ->orderByDesc('ventas.created_at')
            ->get();

        $abonosIngresos = DB::table('apartado_abonos')
            ->leftJoin('apartados', 'apartados.id', '=', 'apartado_abonos.id_apartado')
            ->leftJoin('clientes', 'clientes.id', '=', 'apartados.id_cliente')
            ->whereBetween(
                DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at)'),
                [$fechaInicio->toDateTimeString(), $fechaFin->toDateTimeString()]
            )
            ->select(
                'apartado_abonos.id',
                'apartado_abonos.monto',
                'apartado_abonos.tipo_pago',
                'apartados.nombre_apartado',
                'apartados.id as apartado_id',
                'clientes.nombre as cliente_nombre',
                DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at) as fecha_movimiento')
            )
            ->orderByDesc('fecha_movimiento')
            ->get();

        $totalVentas = (float) $ventasIngresos->sum('monto');
        $totalAbonos = (float) $abonosIngresos->sum('monto');
        $totalIngresos = $totalVentas + $totalAbonos;

        $movimientosIngresos = collect();

        foreach ($ventasIngresos as $venta) {
            $movimientosIngresos->push([
                'fecha' => Carbon::parse($venta->created_at),
                'tipo' => 'VENTA',
                'metodo' => $venta->tipo_pago ?: 'EFECTIVO',
                'referencia' => strlen($venta->cNombreVenta) == 0 ? 'Comprobante de venta folio #' . $venta->id : $venta->cNombreVenta,
                'detalle' => 'Cliente: ' . ($venta->cliente_nombre ?: 'N/A'),
                'monto' => (float) $venta->monto,
            ]);
        }

        foreach ($abonosIngresos as $abono) {
            $movimientosIngresos->push([
                'fecha' => Carbon::parse($abono->fecha_movimiento),
                'tipo' => 'ABONO',
                'metodo' => $abono->tipo_pago ?: 'EFECTIVO',
                'referencia' => strlen($abono->nombre_apartado) == 0 ? 'Comprobante de apartado folio #' . $abono->apartado_id : $abono->nombre_apartado,
                'detalle' => 'Cliente: ' . ($abono->cliente_nombre ?: 'N/A'),
                'monto' => (float) $abono->monto,
            ]);
        }

        $movimientosIngresos = $movimientosIngresos
            ->sortByDesc('fecha')
            ->values();

        $balanceNeto = $totalIngresos - $totalEgresos;

        $nombreArchivo = 'reporte_finanzas_' . $fechaInicio->format('Ymd') . '_' . $fechaFin->format('Ymd') . '.xlsx';

        return Excel::download(new FinanzasExport(
            $fechaInicio->toDateString(),
            $fechaFin->toDateString(),
            $totalIngresos,
            $totalEgresos,
            $balanceNeto,
            $movimientosIngresos,
            $egresos
        ), $nombreArchivo);
    }
}
