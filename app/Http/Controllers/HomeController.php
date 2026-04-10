<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Apartado;
use App\Egreso;
use App\Materiales;
use App\PrecioMateriales;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('inicio');;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->precio_materiales_dia();

        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $precios_oro_gramo = [];

        $precio_oro = PrecioMateriales::join("materiales", "precios_materiales.id_material", "=", "materiales.id")
        ->where('materiales.lActivoConsulta', 1)
        ->where('materiales.cSimbolo', "XAU")
        ->whereDate('precios_materiales.created_at', today())
        ->orderByDesc('precios_materiales.created_at')
        ->first();

        $precio_10k = $precio_oro->json;
        foreach(json_decode($precio_10k) as $key => $value){
            switch($key){
                case "price_gram_10k":
                    $precios_oro_gramo[] = array(
                        'k' => "10k",
                        'v' => $value
                    );
                    $precio_10k = $value;
                break;
                case "price_gram_14k":
                    $precios_oro_gramo[] = array(
                        'k' => "14k",
                        'v' => $value
                    );
                    $precio_14k = $value;
                break;
                // case "price_gram_16k":
                //     $precios_oro_gramo[] = array(
                //         'k' => "16k",
                //         'v' => $value
                //     );
                //     $precio_16k = $value;
                // break;
                // case "price_gram_18k":
                //     $precios_oro_gramo[] = array(
                //         'k' => "18k",
                //         'v' => $value
                //     );
                //     $precio_18k = $value;
                // break;
                // case "price_gram_20k":
                //     $precios_oro_gramo[] = array(
                //         'k' => "20k",
                //         'v' => $value
                //     );
                //     $precio_20k = $value;
                // break;
                // case "price_gram_21k":
                //     $precios_oro_gramo[] = array(
                //         'k' => "21k",
                //         'v' => $value
                //     );
                //     $precio_21k = $value;
                // break;
                // case "price_gram_22k":
                //     $precios_oro_gramo[] = array(
                //         'k' => "22k",
                //         'v' => $value
                //     );
                //     $precio_22k = $value;
                // break;
                case "price_gram_24k":
                    $precios_oro_gramo[] = array(
                        'k' => "24k",
                        'v' => $value
                    );
                    $precio_24k = $value;
                break;
            }
        }

        $totalVentasMes = (float) DB::table('ventas')
            ->join('productos_vendidos', 'productos_vendidos.id_venta', '=', 'ventas.id')
            ->whereBetween('ventas.created_at', [$inicioMes->toDateTimeString(), $finMes->toDateTimeString()])
            ->where(function ($query) {
                $query->whereNull('ventas.tipo_pago')
                    ->orWhereIn('ventas.tipo_pago', ['EFECTIVO', 'MERCADO_PAGO']);
            })
            ->sum(DB::raw('productos_vendidos.cantidad * productos_vendidos.precio'));

        $totalAbonosMes = (float) DB::table('apartado_abonos')
            ->whereBetween(
                DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at)'),
                [$inicioMes->toDateTimeString(), $finMes->toDateTimeString()]
            )
            ->sum('apartado_abonos.monto');

        $ingresosAutomaticosMes = $totalVentasMes + $totalAbonosMes;

        $egresosCapturadosMes = (float) Egreso::whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->sum('monto');

        $balanceNetoMes = $ingresosAutomaticosMes - $egresosCapturadosMes;

        $productosVendidosMes = (float) DB::table('productos_vendidos')
            ->whereBetween('created_at', [$inicioMes->toDateTimeString(), $finMes->toDateTimeString()])
            ->sum('cantidad');

        $apartadosPendientes = (int) Apartado::whereIn('estado', ['ABIERTO', 'PENDIENTE'])
            ->where('saldo', '>', 0)
            ->count();

        // dd($precios_oro_gramo);
        return view('home', compact(
            'precios_oro_gramo',
            'ingresosAutomaticosMes',
            'egresosCapturadosMes',
            'balanceNetoMes',
            'productosVendidosMes',
            'apartadosPendientes',
            'inicioMes',
            'finMes'
        ));
    }

    public function precio_materiales_dia()
    {
        $materiales = Materiales::where('lActivoConsulta', 1)->get();
        foreach ($materiales as $material) {
            $precio = PrecioMateriales::where('id_material', $material->id)
                ->whereDate('created_at', today())
                ->orderByDesc('created_at')
                ->count();

            if ($precio == 0) {

                $apiKey = "goldapi-y1layo19mlb3553g-io";
                $symbol = $material->cSimbolo;
                $curr = "MXN";
                $date = "";

                $myHeaders = array(
                    'x-access-token: ' . $apiKey,
                    'Content-Type: application/json'
                );

                $curl = curl_init();

                $url = "https://www.goldapi.io/api/{$symbol}/{$curr}{$date}";

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTPHEADER => $myHeaders
                ));

                $response = curl_exec($curl);
                $error = curl_error($curl);

                curl_close($curl);
                // $precio = new PrecioMateriales;
                // $precio->id_material = $material->id;
                // $precio->json = json_encode($response);
                // $precio->save();
                PrecioMateriales::create([
                    'id_material' => $material->id,
                    'json' => $response,
                ]);
            }
        }
        return true;
    }

    // endpoint JSON para consultar el precio actual desde JS
    public function goldPrice()
    {
        $gold = PrecioMateriales::table('gold_prices')->orderByDesc('created_at')->first();
        return response()->json($gold ?: []);
    }

    public function inicio()
    {
        return view('about');
    }
}
