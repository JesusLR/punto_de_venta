<?php

namespace App\Http\Controllers;

use App\Apartado;
use App\ApartadoAbono;
use App\ApartadoProducto;
use App\Producto;
use App\Venta;
use App\ProductoVendido;
use App\User;
use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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
            'clientes' => Cliente::orderBy('nombre')->get()
        ]);
    }

    public function abonar(Request $request)
    {
        try {
            $request->validate([
                'id_apartado' => 'required|exists:apartados,id',
                'monto_abono' => 'required|numeric|min:0.01',
                'tipo_pago' => 'required|in:EFECTIVO,MERCADO_PAGO',
                'fecha_abono' => 'required|date_format:Y-m-d',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $e->validator->errors()->first() ?: 'Datos inválidos para registrar el abono.',
                'errors' => $e->validator->errors(),
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
                'fecha_abono' => Carbon::createFromFormat('Y-m-d', $request->fecha_abono)->startOfDay(),
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
                'cNombreVenta' => ($apartado->nombre_apartado == null ? 'Detalle de venta de Apartado #' . $apartado->id : $apartado->nombre_apartado),
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
        $productosDisponibles = Producto::where('lActivo', 1)
            ->where('existencia', '>', 0)
            ->orderBy('descripcion')
            ->get();

        return view('apartados.modals.productos', compact('apartado', 'productosDisponibles'));
    }

    public function agregarProducto(Request $request)
    {
        try {
            $request->validate([
                'id_apartado' => 'required|exists:apartados,id',
                'id_producto' => 'required|exists:productos,id',
                'cantidad' => 'required|numeric|min:0.01',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $e->validator->errors()->first() ?: 'Datos inválidos para agregar el producto.',
                'errors' => $e->validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $apartado = Apartado::findOrFail($request->id_apartado);
            $producto = Producto::findOrFail($request->id_producto);
            $cantidad = (float) $request->cantidad;

            if ((float) $producto->existencia < $cantidad) {
                DB::rollBack();
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => 'No hay existencia suficiente para agregar este producto al apartado.',
                ]);
            }

            $apartadoProducto = ApartadoProducto::where('id_apartado', $apartado->id)
                ->where('id_producto', $producto->id)
                ->first();

            if ($apartadoProducto) {
                $apartadoProducto->cantidad = (float) $apartadoProducto->cantidad + $cantidad;
                $apartadoProducto->save();
            } else {
                ApartadoProducto::create([
                    'id_apartado' => $apartado->id,
                    'id_producto' => $producto->id,
                    'descripcion' => $producto->descripcion,
                    'codigo_barras' => $producto->codigo_barras,
                    'precio' => $producto->precio_venta,
                    'cantidad' => $cantidad,
                ]);
            }

            $producto->existencia = (float) $producto->existencia - $cantidad;
            $producto->save();

            $totalProductos = (float) ApartadoProducto::where('id_apartado', $apartado->id)
                ->selectRaw('COALESCE(SUM(precio * cantidad), 0) as total')
                ->value('total');

            $abonado = (float) ApartadoAbono::where('id_apartado', $apartado->id)->sum('monto');
            $saldo = $totalProductos - $abonado;
            $estado = $saldo <= 0 ? 'LIQUIDADO' : 'ABIERTO';

            $apartado->update([
                'total' => $totalProductos,
                'saldo' => $saldo,
                'estado' => $estado,
            ]);

            DB::commit();
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Producto agregado al apartado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al agregar producto al apartado: ' . $e->getMessage(),
            ]);
        }
    }

    public function verAbonos($id_apartado)
    {
        $apartado = Apartado::with(['abonos.usuario'])->findOrFail($id_apartado);
        $abonos = $apartado->abonos()
            ->with('usuario')
            ->orderByRaw('COALESCE(fecha_abono, created_at) desc')
            ->get();

        return view('apartados.modals.historial_abonos', compact('abonos', 'apartado'));
    }

    public function eliminarAbono(Request $request)
    {
        try {
            $request->validate([
                'id_abono' => 'required|exists:apartado_abonos,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $e->validator->errors()->first() ?: 'Datos inválidos para eliminar el abono.',
                'errors' => $e->validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $abono = ApartadoAbono::findOrFail($request->id_abono);
            $abono->delete();

            DB::commit();
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Abono eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al eliminar el abono: ' . $e->getMessage(),
            ]);
        }
    }

    public function editarFechaAbono(Request $request)
    {
        if (Auth::id() != 1) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'No tienes permiso para editar la fecha del abono.',
            ]);
        }

        try {
            $request->validate([
                'id_abono' => 'required|exists:apartado_abonos,id',
                'fecha_abono' => 'required|date_format:Y-m-d',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $e->validator->errors()->first() ?: 'Datos inválidos para editar la fecha del abono.',
                'errors' => $e->validator->errors(),
            ]);
        }

        DB::beginTransaction();
        try {
            $abono = ApartadoAbono::findOrFail($request->id_abono);
            $abono->fecha_abono = Carbon::createFromFormat('Y-m-d', $request->fecha_abono)->startOfDay();
            $abono->save();

            DB::commit();
            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Fecha de abono actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al editar la fecha del abono: ' . $e->getMessage(),
            ]);
        }
    }

    public function detalle($id)
    {
        $apartado = Apartado::with(['cliente', 'abonos.usuario'])->findOrFail($id);
        $abonos_total = ApartadoAbono::where('id_apartado', $id)->sum('monto');
        $saldo = $apartado->total - $abonos_total;

        $abonos = $apartado->abonos()
            ->with('usuario')
            ->orderByRaw('COALESCE(fecha_abono, created_at) desc')
            ->get();

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
                        'fecha' => optional($abono->fecha_registro)->format('d/m/Y') ?? '-',
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
        }

        if ($request->cTipoBusqueda && $request->cTipoBusqueda != "T") {
            $apartados->where('apartados.id_cliente', $request->cTipoBusqueda);
        }

        if ($request->cEstadoApartado && $request->cEstadoApartado != "T") {
            $apartados->where('apartados.estado', $request->cEstadoApartado);
        }

        // if ($request->cFechaInicioApartado) {
        //     $apartados->whereDate('apartados.created_at', '>=', $request->cFechaInicioApartado);
        // }

        // if ($request->cFechaFinApartado) {
        //     $apartados->whereDate('apartados.created_at', '<=', $request->cFechaFinApartado);
        // }

        $apartados = $apartados->orderBy('apartados.id', 'desc')
            ->get()
            ->map(function ($apartado) {
                $abonos_total = ApartadoAbono::where('id_apartado', $apartado->id)->sum('monto');
                $saldo_actual = $apartado->total - $abonos_total;
                $estado_actual = $saldo_actual <= 0 ? 'LIQUIDADO' : 'ABIERTO';

                return [
                    'id' => $apartado->id,
                    'id_cliente' => $apartado->id_cliente,
                    'cliente' => $apartado->cliente ? $apartado->cliente : 'N/A',
                    'name' => $apartado->vendedor,
                    'total' => (float) $apartado->total,
                    'abonado' => (float) $abonos_total,
                    'saldo' => (float) $saldo_actual,
                    'estado' => $estado_actual,
                    'created_at' => $apartado->created_at,
                    'nombre_apartado' => $apartado->nombre_apartado,
                ];
            });

        return $apartados;
    }

    public function cambiarNombre(Request $request)
    {
        try {
            $request->validate([
                'id_apartado' => 'required|exists:apartados,id',
                'nombre_apartado' => 'nullable|string|max:255',
                'id_cliente' => 'required|exists:clientes,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $e->validator->errors()->first() ?: 'Datos inválidos para cambiar el nombre del apartado.',
                'errors' => $e->validator->errors(),
            ]);
        }

        $apartado = Apartado::findOrFail($request->id_apartado);
        $apartado->update([
            'nombre_apartado' => strtoupper($request->nombre_apartado),
            'id_cliente' => $request->id_cliente,
        ]);

        return response()->json([
            'lSuccess' => true,
            'cMensaje' => 'Nombre del apartado actualizado exitosamente.',
        ]);
    }
}
