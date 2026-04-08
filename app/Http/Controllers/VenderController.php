<?php
/*

  ____          _____               _ _           _
 |  _ \        |  __ \             (_) |         | |
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |
        |___/                               |___/

    Blog:       https://parzibyte.me/blog
    Ayuda:      https://parzibyte.me/blog/contrataciones-ayuda/
    Contacto:   https://parzibyte.me/blog/contacto/

    Copyright (c) 2020 Luis Cabrera Benito
    Licenciado bajo la licencia MIT

    El texto de arriba debe ser incluido en cualquier redistribucion
*/ ?>
<?php

namespace App\Http\Controllers;

use App\Apartado;
use App\ApartadoAbono;
use App\ApartadoProducto;
use App\Cliente;
use App\Producto;
use App\ProductoVendido;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VenderController extends Controller
{

    public function crearClienteRapido(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $cliente = Cliente::create([
                'nombre' => $request->input('nombre'),
                'telefono' => $request->input('telefono'),
                'observaciones' => $request->input('observaciones'),
            ]);

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Cliente agregado correctamente.',
                'cliente' => [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'telefono' => $cliente->telefono,
                    'observaciones' => $cliente->observaciones,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => 'No se pudo guardar el cliente.',
            ]);
        }
    }

    public function terminarOCancelarVenta(Request $request)
    {
        if ($request->input("accion") == "terminar") {
            return $this->terminarVenta($request);
        } elseif ($request->input("accion") == "apartar") {
            return $this->apartarVenta($request);
        } else {
            return $this->cancelarVenta();
        }
    }

    public function apartarVenta(Request $request)
    {
        $productos = $this->obtenerProductos();
        if (count($productos) <= 0) {
            return redirect()->route("vender.index")->with([
                "mensaje" => "No hay productos para apartar",
                "tipo" => "danger",
            ]);
        }

        DB::beginTransaction();
        try {
            $apartado = new Apartado();
            $apartado->id_cliente = $request->input("id_cliente");
            $apartado->id_usuario = $request->input("userID");
            $apartado->total = $this->calcularTotalCarrito($productos);
            $apartado->total_abonado = 0;
            $apartado->saldo = $apartado->total;
            $apartado->estado = 'ABIERTO';
            $apartado->saveOrFail();

            foreach ($productos as $producto) {
                $productoActualizado = Producto::find($producto->id);
                if (!$productoActualizado || $productoActualizado->existencia < $producto->cantidad) {
                    throw new \Exception('No hay existencia suficiente para apartar el producto ' . $producto->descripcion);
                }

                $apartadoProducto = new ApartadoProducto();
                $apartadoProducto->fill([
                    "id_apartado" => $apartado->id,
                    "id_producto" => $producto->id,
                    "descripcion" => $producto->descripcion,
                    "codigo_barras" => $producto->codigo_barras,
                    "precio" => $producto->precio_venta,
                    "cantidad" => $producto->cantidad,
                ]);
                $apartadoProducto->saveOrFail();

                $productoActualizado->existencia -= $producto->cantidad;
                $productoActualizado->saveOrFail();
            }

            DB::commit();
            $this->vaciarProductos();

            return redirect()->route("vender.index")->with("mensaje", "Productos apartados correctamente");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route("vender.index")->with([
                "mensaje" => $e->getMessage(),
                "tipo" => "danger",
            ]);
        }
    }

    public function abonarApartado(Request $request)
    {
        return redirect()->route('apartados.index');
    }

    public function ejecutarApartadoVenta(Request $request)
    {
        return redirect()->route('apartados.index');
    }

    public function terminarVenta(Request $request)
    {
        $request->all();
        $venta = new Venta();
        $venta->cNombreVenta = NULL;
        $venta->id_cliente = $request->input("id_cliente");
        $venta->id_usuario = $request->input("userID");
        $venta->saveOrFail();
        $idVenta = $venta->id;
        $productos = $this->obtenerProductos();
        // Recorrer carrito de compras
        foreach ($productos as $producto) {
            // El producto que se vende...
            $productoVendido = new ProductoVendido();
            $productoVendido->fill([
                "id_venta" => $idVenta,
                "descripcion" => $producto->descripcion,
                "codigo_barras" => $producto->codigo_barras,
                "precio" => $producto->precio_venta,
                "cantidad" => $producto->cantidad,
            ]);
            // Lo guardamos
            $productoVendido->saveOrFail();
            // Y restamos la existencia del original
            $productoActualizado = Producto::find($producto->id);
            $productoActualizado->existencia -= $productoVendido->cantidad;
            $productoActualizado->saveOrFail();
        }
        $this->vaciarProductos();
        return redirect()
            ->route("vender.index")
            ->with("mensaje", "Venta terminada");
    }

    private function calcularTotalCarrito(array $productos)
    {
        $total = 0;
        foreach ($productos as $producto) {
            $total += $producto->cantidad * $producto->precio_venta;
        }
        return $total;
    }

    private function obtenerProductos()
    {
        $productos = session("productos");
        if (!$productos) {
            $productos = [];
        }
        return $productos;
    }

    private function vaciarProductos()
    {
        $this->guardarProductos(null);
    }

    private function guardarProductos($productos)
    {
        session(["productos" => $productos,
        ]);
    }

    public function cancelarVenta()
    {
        $this->vaciarProductos();
        return redirect()
            ->route("vender.index")
            ->with("mensaje", "Venta cancelada");
    }

    public function quitarProductoDeVenta(Request $request)
    {
        $indice = $request->post("indice");
        $productos = $this->obtenerProductos();
        array_splice($productos, $indice, 1);
        $this->guardarProductos($productos);
        return redirect()
            ->route("vender.index");
    }

    public function agregarProductoVenta(Request $request)
    {
        $codigo = $request->post("codigo");
        $producto = Producto::where("id", "=", $codigo)->first();
        if (!$producto) {
            return redirect()
                ->route("vender.index")
                ->with("mensaje", "Producto no encontrado");
        }
        $this->agregarProductoACarrito($producto);
        return redirect()
            ->route("vender.index");
    }

    private function agregarProductoACarrito($producto)
    {
        if ($producto->existencia <= 0) {
            return redirect()->route("vender.index")
                ->with([
                    "mensaje" => "No hay existencias del producto",
                    "tipo" => "danger"
                ]);
        }
        $productos = $this->obtenerProductos();
        $posibleIndice = $this->buscarIndiceDeProducto($producto->codigo_barras, $productos);
        // Es decir, producto no fue encontrado
        if ($posibleIndice === -1) {
            $producto->cantidad = 1;
            array_push($productos, $producto);
        } else {
            if ($productos[$posibleIndice]->cantidad + 1 > $producto->existencia) {
                return redirect()->route("vender.index")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia",
                        "tipo" => "danger"
                    ]);
            }
            $productos[$posibleIndice]->cantidad++;
        }
        $this->guardarProductos($productos);
    }

    private function buscarIndiceDeProducto(string $codigo, array &$productos)
    {
        foreach ($productos as $indice => $producto) {
            if ($producto->codigo_barras === $codigo) {
                return $indice;
            }
        }
        return -1;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productosCarrito = $this->obtenerProductos();
        $total = $this->calcularTotalCarrito($productosCarrito);

        return view("vender.vender",
            [
                "total" => $total,
                "clientes" => Cliente::all(),
                "productos" => Producto::where('lActivo', 1)->where('existencia', '>', 0)->get(),
            ]);
    }
}
