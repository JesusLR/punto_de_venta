<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Apartado;
use App\Egreso;
use App\Materiales;
use App\PrecioMateriales;
use App\Producto;
use App\Venta;

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

        $productosVendidosMes = (int) round(
            (float) DB::table('ventas')
                ->join('productos_vendidos', 'productos_vendidos.id_venta', '=', 'ventas.id')
                ->whereBetween('ventas.created_at', [
                    $inicioMes->toDateTimeString(),
                    $finMes->toDateTimeString()
                ])
                ->when(auth()->id() != 1, fn ($query) => $query->where('ventas.id_usuario', auth()->id()))
                ->sum('productos_vendidos.cantidad')
        );

        $apartadosPendientes = (int) Apartado::whereIn('estado', ['ABIERTO', 'PENDIENTE'])
            ->where('saldo', '>', 0)
            ->when(auth()->id() != 1, function ($query) {
                $query->where('id_usuario', auth()->id());
            })
            ->count();

        // 1. Estadísticas de Flujo de Caja (últimos 15 días)
        $ventasDias = DB::table('ventas')
            ->join('productos_vendidos', 'productos_vendidos.id_venta', '=', 'ventas.id')
            ->select(DB::raw('DATE(ventas.created_at) as fecha'), DB::raw('SUM(productos_vendidos.cantidad * productos_vendidos.precio) as total'))
            ->where('ventas.created_at', '>=', Carbon::now()->subDays(15)->startOfDay())
            ->where(function ($query) {
                $query->whereNull('ventas.tipo_pago')
                    ->orWhereIn('ventas.tipo_pago', ['EFECTIVO', 'MERCADO_PAGO']);
            })
            ->groupBy(DB::raw('DATE(ventas.created_at)'))
            ->get();

        $abonosDias = DB::table('apartado_abonos')
            ->select(DB::raw('DATE(COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at)) as fecha'), DB::raw('SUM(apartado_abonos.monto) as total'))
            ->where(DB::raw('COALESCE(apartado_abonos.fecha_abono, apartado_abonos.created_at)'), '>=', Carbon::now()->subDays(15)->startOfDay())
            ->groupBy('fecha')
            ->get();

        $egresosDias = DB::table('egresos')
            ->select('fecha', DB::raw('SUM(monto) as total'))
            ->where('fecha', '>=', Carbon::now()->subDays(15)->toDateString())
            ->groupBy('fecha')
            ->get();

        $chartData = [];
        for ($i = 14; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->toDateString();
            $dateFormatted = Carbon::now()->subDays($i)->format('d M');
            $chartData[$dateStr] = [
                'fecha' => $dateFormatted,
                'ingresos' => 0.0,
                'egresos' => 0.0
            ];
        }

        foreach ($ventasDias as $v) {
            $f = Carbon::parse($v->fecha)->toDateString();
            if (isset($chartData[$f])) {
                $chartData[$f]['ingresos'] += (float) $v->total;
            }
        }
        foreach ($abonosDias as $a) {
            $f = Carbon::parse($a->fecha)->toDateString();
            if (isset($chartData[$f])) {
                $chartData[$f]['ingresos'] += (float) $a->total;
            }
        }
        foreach ($egresosDias as $e) {
            $f = Carbon::parse($e->fecha)->toDateString();
            if (isset($chartData[$f])) {
                $chartData[$f]['egresos'] += (float) $e->total;
            }
        }
        $dailyStats = array_values($chartData);

        // 2. Ventas por Categoría (mes en curso)
        $categoriasPopulares = DB::table('productos_vendidos')
            ->join('ventas', 'productos_vendidos.id_venta', '=', 'ventas.id')
            ->join('productos', 'productos_vendidos.codigo_barras', '=', 'productos.codigo_barras')
            ->join('categorias', 'productos.id_categoria', '=', 'categorias.id')
            ->select('categorias.cNombreCategoria as categoria', DB::raw('SUM(productos_vendidos.cantidad * productos_vendidos.precio) as total'))
            ->whereBetween('ventas.created_at', [$inicioMes->toDateTimeString(), $finMes->toDateTimeString()])
            ->groupBy('categorias.id', 'categorias.cNombreCategoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 3. Productos con Bajo Stock (existencia <= 3)
        $productosBajoStock = Producto::with(['Materiales', 'Categorias'])
            ->where('existencia', '<=', 3)
            ->orderBy('existencia', 'asc')
            ->limit(5)
            ->get();

        // 4. Ventas Recientes (últimas 5)
        $ventasRecientes = Venta::with(['cliente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($venta) {
                $total = DB::table('productos_vendidos')
                    ->where('id_venta', $venta->id)
                    ->sum(DB::raw('cantidad * precio'));
                return [
                    'id' => $venta->id,
                    'cliente' => $venta->cliente ? $venta->cliente->nombre : 'Público General',
                    'vendedor' => $venta->usuario ? $venta->usuario->name : 'N/A',
                    'tipo_pago' => $venta->tipo_pago ?: 'EFECTIVO',
                    'total' => (float)$total,
                    'fecha' => $venta->created_at->format('d/m H:i'),
                    'hace_tiempo' => $venta->created_at->diffForHumans()
                ];
            });

        // 5. Historial del precio del oro (últimos 7 registros)
        $oroHistoricoRaw = PrecioMateriales::join("materiales", "precios_materiales.id_material", "=", "materiales.id")
            ->where('materiales.lActivoConsulta', 1)
            ->where('materiales.cSimbolo', "XAU")
            ->select('precios_materiales.created_at', 'precios_materiales.json')
            ->orderBy('precios_materiales.created_at', 'desc')
            ->limit(7)
            ->get();

        $oroHistorico = [];
        foreach ($oroHistoricoRaw->reverse() as $h) {
            $decoded = json_decode($h->json);
            if ($decoded) {
                $oroHistorico[] = [
                    'fecha' => Carbon::parse($h->created_at)->format('d/m'),
                    'price_10k' => isset($decoded->price_gram_10k) ? (float) $decoded->price_gram_10k : 0.0,
                    'price_14k' => isset($decoded->price_gram_14k) ? (float) $decoded->price_gram_14k : 0.0,
                    'price_24k' => isset($decoded->price_gram_24k) ? (float) $decoded->price_gram_24k : 0.0,
                ];
            }
        }

        return view('home', compact(
            'precios_oro_gramo',
            'ingresosAutomaticosMes',
            'egresosCapturadosMes',
            'balanceNetoMes',
            'productosVendidosMes',
            'apartadosPendientes',
            'inicioMes',
            'finMes',
            'dailyStats',
            'categoriasPopulares',
            'productosBajoStock',
            'ventasRecientes',
            'oroHistorico'
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
        $settings = \App\HomepageSetting::pluck('value', 'key')->toArray();
        return view('about', compact('settings'));
    }

    public function obtenerNotificaciones()
    {
        try {
            $notifications = [];

            // Obtener notificaciones ya leídas/descartadas por el usuario actual
            $readNotifications = \DB::table('notificaciones_leidas')
                ->where('user_id', \Auth::id())
                ->get()
                ->groupBy('notification_type')
                ->map(function ($items) {
                    return $items->pluck('notification_id')->toArray();
                })
                ->toArray();

            $readApartados = $readNotifications['apartado_inactivo'] ?? [];
            $readProducts = $readNotifications['stock_bajo'] ?? [];

            // 1. Apartados Inactivos (Reminders)
            $diasInactividad = (int) \App\HomepageSetting::getValue('apartado_inactive_days', 30);
            $limitDate = now()->subDays($diasInactividad);

            $apartados = Apartado::where('estado', 'ABIERTO')
                ->whereNotIn('id', $readApartados)
                ->where('updated_at', '<', $limitDate)
                ->whereDoesntHave('abonos', function ($query) use ($limitDate) {
                    $query->where('fecha_abono', '>=', $limitDate)
                          ->orWhere('created_at', '>=', $limitDate);
                })
                ->with(['cliente'])
                ->orderBy('updated_at', 'asc')
                ->get();

            foreach ($apartados as $ap) {
                $ultimoAbono = $ap->abonos()->latest('fecha_abono')->first();
                $fechaReferencia = $ultimoAbono ? Carbon::parse($ultimoAbono->fecha_abono) : Carbon::parse($ap->created_at);
                $diasTranscurridos = now()->diffInDays($fechaReferencia);
                $clienteNombre = $ap->cliente ? $ap->cliente->nombre : 'Cliente General';
                $apNombre = $ap->nombre_apartado ?: 'Sin nombre';

                $notifications[] = [
                    'id' => $ap->id,
                    'type' => 'apartado_inactivo',
                    'icon' => 'fas fa-exclamation-triangle',
                    'title' => 'Apartado sin movimiento',
                    'description' => "El apartado <strong>\"{$apNombre}\"</strong> de <strong>{$clienteNombre}</strong> lleva <strong>{$diasTranscurridos} días</strong> sin abonos (Saldo pendiente: $" . number_format($ap->saldo, 2) . ").",
                    'time_ago' => "Último abono: " . $fechaReferencia->format('d/m/Y'),
                    'url' => route('apartados.index') . "?ver_abonos={$ap->id}"
                ];
            }

            /*
            // 2. Productos con Bajo Stock (stock_bajo)
            $productosBajoStock = \App\Producto::where('lActivo', 1)
                ->whereNotIn('id', $readProducts)
                ->where('existencia', '<=', 3)
                ->orderBy('existencia', 'asc')
                ->take(5)
                ->get();

            foreach ($productosBajoStock as $prod) {
                $notifications[] = [
                    'id' => $prod->id,
                    'type' => 'stock_bajo',
                    'icon' => 'fas fa-box-open',
                    'title' => 'Bajo stock de producto',
                    'description' => "El producto <strong>\"{$prod->descripcion}\"</strong> tiene un stock muy bajo: <strong>" . (float)$prod->existencia . " unidades</strong>.",
                    'time_ago' => "Código: {$prod->codigo_barras}",
                    'url' => route('productos.index') . "?buscar={$prod->codigo_barras}"
                ];
            }
            */

            return response()->json([
                'lSuccess' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al obtener notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar una notificación como leída (guardar en base de datos para excluirla de la lista).
     */
    public function marcarNotificacionLeida(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'id' => 'required'
            ]);

            \DB::table('notificaciones_leidas')->updateOrInsert(
                [
                    'user_id' => \Auth::id(),
                    'notification_type' => $request->type,
                    'notification_id' => $request->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            return response()->json([
                'lSuccess' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'Error al marcar como leída: ' . $e->getMessage()
            ], 500);
        }
    }
}
