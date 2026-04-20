<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

use App\ProductoVendido;

class EstadisticasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('estadisticas.estadisticas_index');
    }

    public function graficaVentas(Request $request){
        try{

            $filtros = $this->resolverFiltros($request);

            if ($filtros['producto'] !== 'T') {
                $query = ProductoVendido::query();

                if ($filtros['filtroUnidades'] !== 'GENERAL') {
                    $query->whereBetween('created_at', [$filtros['fechaInicio'], $filtros['fechaFin']]);
                }

                $resultado = $query->where('codigo_barras', $filtros['producto'])
                    ->selectRaw('DATE(created_at) as fecha, SUM(cantidad) as total_cantidad')
                    ->groupBy('fecha')
                    ->orderBy('fecha', 'asc')
                    ->get();

                $productos = $resultado->map(function ($item) {
                    return [
                        'codigo_barras' => $item->fecha,
                        'descripcion' => $item->fecha,
                        'total_cantidad' => (float) $item->total_cantidad,
                    ];
                });
            } else {
                $resultado = $this->consultaProductosVendidos(
                    $filtros['fechaInicio'],
                    $filtros['fechaFin'],
                    $filtros['filtroUnidades'],
                    null,
                    'AGREGADO'
                )->get();

                $productos = $resultado->map(function ($producto) {
                    return [
                        'codigo_barras' => $producto->codigo_barras,
                        'descripcion' => $producto->descripcion,
                        'total_cantidad' => (float) $producto->total_unidades,
                    ];
                });
            }

            // Devolver la respuesta en formato JSON
            return response()->json([
                'lSuccess' => true,
                'productos' => $productos,
                'cMensaje' => count($productos) === 0 ? 'Sin registros para los filtros seleccionados' : '',
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'lSuccess'        => false,
                'cMensaje'        => $th->getMessage(),
            ]);
        }
    }

    public function gridEstadisticas(Request $request){
        try{
            $filtros = $this->resolverFiltros($request);

            $modo = $filtros['producto'] === 'T' ? 'AGREGADO' : 'HISTORICO';

            $resultado = $this->consultaProductosVendidos(
                $filtros['fechaInicio'],
                $filtros['fechaFin'],
                $filtros['filtroUnidades'],
                $filtros['producto'],
                $modo
            )->get();

            $resumenProductos = $this->consultaProductosVendidos(
                $filtros['fechaInicio'],
                $filtros['fechaFin'],
                $filtros['filtroUnidades'],
                $filtros['producto'],
                $modo
            )->get();

            return response()->json([
                'lSuccess' => true,
                'productos' => $resultado,
                'modo' => $modo,
                'resumen' => [
                    'total_productos' => (int) ($modo === 'AGREGADO' ? $resumenProductos->count() : $resumenProductos->pluck('codigo_barras')->unique()->count()),
                    'total_unidades' => (float) $resumenProductos->sum('total_unidades'),
                    'total_vendido' => (float) $resumenProductos->sum('total_vendido'),
                ],
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $th->getMessage(),
            ]);
        }
    }

    public function productosVendidosFiltro(Request $request)
    {
        try {
            $productos = ProductoVendido::query()
                ->select(
                    'productos_vendidos.codigo_barras',
                    'productos_vendidos.descripcion',
                    \DB::raw('COALESCE(productos.existencia, 0) as existencia')
                )
                ->leftJoin('productos', 'productos.codigo_barras', '=', 'productos_vendidos.codigo_barras')
                ->whereNotNull('productos_vendidos.codigo_barras')
                ->whereNotNull('productos_vendidos.descripcion')
                ->groupBy('productos_vendidos.codigo_barras', 'productos_vendidos.descripcion', 'productos.existencia')
                ->orderBy('productos_vendidos.descripcion', 'asc')
                ->get();

            return response()->json([
                'lSuccess' => true,
                'productos' => $productos,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $th->getMessage(),
            ]);
        }
    }

    private function resolverFiltros(Request $request)
    {
        $fechaInicio = Carbon::parse($request->dtFechaInicio)->startOfDay();
        $fechaFin = Carbon::parse($request->dtFechaFinal)->endOfDay();

        if ($fechaInicio->greaterThan($fechaFin)) {
            throw new Exception('La fecha inicio no puede ser mayor que la fecha final');
        }

        $filtroUnidades = strtoupper((string) $request->get('cFiltroUnidades', 'RANGO'));
        if (!in_array($filtroUnidades, ['RANGO', 'GENERAL'])) {
            $filtroUnidades = 'RANGO';
        }

        $producto = (string) $request->get('cProducto', 'T');
        if (trim($producto) === '') {
            $producto = 'T';
        }

        return [
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'filtroUnidades' => $filtroUnidades,
            'producto' => $producto,
        ];
    }

    private function consultaProductosVendidos($fechaInicio, $fechaFin, $filtroUnidades, $producto = 'T', $modo = 'AGREGADO')
    {
        $query = ProductoVendido::query();

        if ($filtroUnidades !== 'GENERAL') {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        if ($producto !== 'T') {
            $query->where('codigo_barras', $producto);
        }

        if ($modo === 'HISTORICO') {
            return $query
                ->selectRaw('DATE(created_at) as fecha_movimiento, codigo_barras, descripcion, SUM(cantidad) as total_unidades, AVG(precio) as precio_promedio, SUM(cantidad * precio) as total_vendido')
                ->groupBy('fecha_movimiento', 'codigo_barras', 'descripcion')
                ->orderByDesc('fecha_movimiento');
        }

        $query->groupBy('codigo_barras', 'descripcion')
            ->selectRaw('codigo_barras, descripcion, SUM(cantidad) as total_unidades, AVG(precio) as precio_promedio, SUM(cantidad * precio) as total_vendido')
            ->orderByDesc('total_unidades');

        return $query;
    }

}
