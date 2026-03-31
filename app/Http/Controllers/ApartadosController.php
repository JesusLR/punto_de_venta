<?php

namespace App\Http\Controllers;

use App\Apartado;
use App\ApartadoAbono;
use App\Venta;
use App\ProductoVendido;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDF;

class ApartadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('apartados.index', [
            'users' => User::select('*')->get()
        ]);
    }

    public function abonar(Request $request)
    {
        try {
            $request->validate([
                'id_apartado' => 'required|exists:apartados,id',
                'monto_abono' => 'required|numeric|min:0.01',
                'tipo_pago' => 'required|in:EFECTIVO,MERCADO_PAGO',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Datos inválidos para registrar el abono.',
            ]);
        }

        $apartado = Apartado::findOrFail($request->id_apartado);

        $total_abonado = $apartado->abonos()->sum('monto');
        $saldo = $apartado->total - $total_abonado;

        if ($request->monto_abono > $saldo) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'El monto del abono no puede ser mayor al saldo.',
            ]);
        }

        DB::beginTransaction();
        try {
            $abono = ApartadoAbono::create([
                'id_apartado' => $apartado->id,
                'id_usuario' => Auth::id(),
                'monto' => $request->monto_abono,
                'tipo_pago' => $request->tipo_pago,
                'observaciones' => $request->observaciones ?? null,
            ]);

            DB::commit();
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Abono registrado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al registrar el abono: ' . $e->getMessage(),
            ]);
        }
    }

    public function ejecutar(Request $request)
    {
        try {
            $request->validate([
                'id_apartado' => 'required|exists:apartados,id',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Datos inválidos para ejecutar el apartado.',
            ]);
        }

        $apartado = Apartado::findOrFail($request->id_apartado);

        $total_abonado = $apartado->abonos()->sum('monto');
        $saldo = $apartado->total - $total_abonado;

        if ($saldo > 0) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'El apartado no está completamente pagado.',
            ]);
        }

        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'id_cliente' => $apartado->id_cliente,
                'id_usuario' => $apartado->id_usuario,
                'cNombreVenta' => 'Detalle de venta de Apartado #' . $apartado->id,
            ]);

            foreach ($apartado->productos as $producto_apartado) {
                ProductoVendido::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $producto_apartado->id_producto,
                    'descripcion' => $producto_apartado->descripcion,
                    'codigo_barras' => $producto_apartado->codigo_barras,
                    'precio' => $producto_apartado->precio,
                    'cantidad' => $producto_apartado->cantidad,
                ]);
            }

            $apartado->update([
                'estado' => 'FINALIZADO',
                'id_venta' => $venta->id,
            ]);

            DB::commit();
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Venta ejecutada exitosamente. Folio: #' . $venta->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al ejecutar la venta: ' . $e->getMessage(),
            ]);
        }
    }

    public function pdf($id_apartado)
    {
        $apartado = Apartado::with(['cliente', 'usuario', 'productos', 'abonos.usuario'])->findOrFail($id_apartado);
        $totalAbonado = $apartado->abonos->sum('monto');
        $saldo = $apartado->total - $totalAbonado;

        $pdf = PDF::loadView('apartados.pdf', [
            'apartado' => $apartado,
            'totalAbonado' => $totalAbonado,
            'saldo' => $saldo,
        ])->setPaper('letter', 'portrait');

        return $pdf->stream('apartado_' . $apartado->id . '.pdf');
    }

    public function verProductos($id_apartado)
    {
        $apartado = Apartado::with('productos')->findOrFail($id_apartado);
        return view('apartados.productos-modal', compact('apartado'));
    }

    public function verAbonos($id_apartado)
    {
        $apartado = Apartado::with(['abonos.usuario'])->findOrFail($id_apartado);
        $abonos = $apartado->abonos()->with('usuario')->orderBy('created_at', 'desc')->get();

        return view('apartados.abonos-modal', compact('abonos'));
    }

    public function detalle($id)
    {
        $apartado = Apartado::with(['cliente', 'abonos.usuario'])->findOrFail($id);
        $abonos_total = ApartadoAbono::where('id_apartado', $id)->sum('monto');
        $saldo = $apartado->total - $abonos_total;

        $abonos = $apartado->abonos()->with('usuario')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'lSuccess' => true,
            'data' => [
                'id' => $apartado->id,
                'cliente' => $apartado->cliente->nombre ?? 'N/A',
                'total' => (float) $apartado->total,
                'abonado' => (float) $abonos_total,
                'saldo' => (float) $saldo,
                'abonos' => $abonos->map(function ($abono) {
                    return [
                        'fecha' => $abono->created_at->format('d/m/Y H:i:s'),
                        'usuario' => $abono->usuario->name,
                        'monto' => (float) $abono->monto,
                        'tipo_pago' => $abono->tipo_pago === 'MERCADO_PAGO' ? 'MERCADO PAGO' : 'EFECTIVO',
                        'observaciones' => $abono->observaciones ?? '-',
                    ];
                })->toArray(),
            ]
        ]);
    }

    public function gridApartados(Request $request)
    {
        $apartados = Apartado::query();
        $apartados->join("clientes", "clientes.id", "=", "apartados.id_cliente")
            ->join("users", "users.id", "=", "apartados.id_usuario")
            ->select("apartados.*", "clientes.nombre as cliente", "users.name as vendedor")
            ->whereIn('apartados.estado', ['ABIERTO', 'LIQUIDADO']);

        if (Auth::id() != 1) {
            $apartados->where('apartados.id_usuario', Auth::id());
        } else if ($request->cTipoBusqueda && $request->cTipoBusqueda != "T") {
            $apartados->where('apartados.id_usuario', $request->cTipoBusqueda);
        }

        $apartados = $apartados->orderBy('apartados.id', 'desc')
            ->get()
            ->map(function ($apartado) {
                $abonos_total = ApartadoAbono::where('id_apartado', $apartado->id)->sum('monto');
                $saldo_actual = $apartado->total - $abonos_total;
                $estado_actual = $saldo_actual <= 0 ? 'LIQUIDADO' : 'ABIERTO';

                return [
                    'id' => $apartado->id,
                    'cliente' => $apartado->cliente ? $apartado->cliente : 'N/A',
                    'name' => $apartado->vendedor,
                    'total' => (float) $apartado->total,
                    'abonado' => (float) $abonos_total,
                    'saldo' => (float) $saldo_actual,
                    'estado' => $estado_actual,
                    'created_at' => $apartado->created_at,
                ];
            });

        return $apartados;
    }
}
