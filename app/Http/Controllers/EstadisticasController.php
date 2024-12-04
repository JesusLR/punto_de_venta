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

            $fechaInicio = Carbon::parse($request->dtFechaInicio); // fecha de inicio
            $fechaFin = Carbon::parse($request->dtFechaFinal); // fecha de fin
            // dd($fechaInicioA, $fechaInicio);
            $resultado = ProductoVendido::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('codigo_barras')  // Agrupar por el código de barras
            ->selectRaw('codigo_barras, sum(cantidad) as total_cantidad, count(*) as veces_vendido')  // Sumar las cantidades y contar las veces
            ->orderByDesc('total_cantidad')  // Ordenar por la suma total de la cantidad de manera descendente
            ->take(10)  // Limitar los resultados a los primeros 10
            ->get();

            if(count($resultado) == 0){
                throw new Exception("No se encontraron registrso de venta");
            }

            // Preparar los datos para el gráfico
            $productos = $resultado->map(function ($producto) {
                return [
                    'codigo_barras' => $producto->codigo_barras,
                    'total_cantidad' => $producto->total_cantidad,
                ];
            });

            // Devolver la respuesta en formato JSON
            return response()->json([
                'lSuccess' => true,
                'productos' => $productos,
            ]);

        }catch(\Throwable $th){
            return response()->json([
                'lSuccess'        => false,
                'cMensaje'        => $th->getMessage(),
            ]);
        }
    }

}
