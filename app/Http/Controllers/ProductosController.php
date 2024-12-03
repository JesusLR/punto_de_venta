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

use App\Producto;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\importProductos;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("productos.productos_index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("productos.productos_create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = new Producto();
        $producto->codigo_barras = $request->codigo_barras;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra;
        $producto->precio_venta = $request->precio_venta;
        $producto->existencia = $request->existencia;
        $ext = $request->img->extension();
        $request->img->move(public_path('img/productos'), "producto_".$request->codigo_barras.".".$ext);
        $producto->img = "producto_".$request->codigo_barras.".".$ext;
        $producto->lActivo = 1;
        $producto->save();
        return redirect()->route("productos.index")->with("mensaje", "Producto guardado");
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function editarProducto($id)
    {
        $producto = Producto::where('id', $id)->first();
        return view("productos.productos_edit", ["producto" => $producto]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $producto = Producto::where('id', $producto->id)->first();
        $producto->codigo_barras = $request->codigo_barras;
        $producto->descripcion = $request->descripcion;
        $producto->precio_compra = $request->precio_compra;
        $producto->precio_venta = $request->precio_venta;
        $producto->existencia = $request->existencia;

        if(isset($request->img)){
            $ext = $request->img->extension();
            $request->img->move(public_path('img/productos'), "producto_".$request->codigo_barras.".".$ext);
            $producto->img = "producto_".$request->codigo_barras.".".$ext;
        }

        $producto->save();

        return redirect()->route("productos.index")->with("mensaje", "Producto actualizado");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Producto $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route("productos.index")->with("mensaje", "Producto eliminado");
    }

    public function gridProductos(Request $request){
        try{

            switch($request->cTipoBusqueda){
                case 'T':
                    $productos = Producto::where('lActivo', 1)->get();
                break;
                case 'B':
                    $productos = Producto::where('lActivo', 1)->where('existencia', '>', 4)->get();
                break;
                case 'R':
                    $productos = Producto::where('lActivo', 1)->where('existencia', '<', 1)->get();
                break;
                case 'N':
                    $productos = Producto::where('lActivo', 1)->where('existencia', '>', 0)->where('existencia', '<', 4)->get();
                break;
            }

            return  $productos;

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function getProductos(){
        try{

            return  Producto::where('lActivo', 1)->get();

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function deleteProducto(Request $request){
        try{

            $producto = Producto::where('id', $request->id)->first();
            $producto->lActivo = 0;
            $producto->save();

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Producto eliminado!',
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function cargarProductosExcell(Request $request){
        try{

            // dd($request->all());

            $request->validate([
                'fileExcell' => 'required|mimes:xlsx,xls'
            ]);

            $file = $request->file('fileExcell');

            // Usar la clase de importación
            Excel::import(new importProductos, $file);


            return response()->json([
                'lSuccess' => true,
                'cMensaje' => 'Producto creados con exito',
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }
}
