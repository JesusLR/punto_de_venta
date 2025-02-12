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
use App\Categorias;
use App\Materiales;
use App\Proveedores;
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
        $lstCategorias = Categorias::where('lActivo', 1)->get();
        $lstMateriales = Materiales::where('lActivo', 1)->get();
        $lstProveedores = Proveedores::where('lActivo', 1)->get();
        
        return view("productos.productos_index", [
            'lstCategorias' => $lstCategorias,
            'lstMateriales' => $lstMateriales,
            'lstProveedores' => $lstProveedores
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lstCategorias = Categorias::where('lActivo', 1)->get();
        $lstMateriales = Materiales::where('lActivo', 1)->get();
        $lstProveedores = Proveedores::where('lActivo', 1)->get();

        return view("productos.productos_create", [
            'lstCategorias' => $lstCategorias,
            'lstMateriales' => $lstMateriales,
            'lstProveedores' => $lstProveedores
        ]);
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
        $producto->id_categoria = $request->id_categoria;
        $producto->id_material = $request->id_material;
        $producto->id_proveedor = $request->id_proveedor;
        // $ext = $request->img->extension();
        // $request->img->move(public_path('img/productos'), "producto_".$request->codigo_barras.".".$ext);
        // $producto->img = "producto_".$request->codigo_barras.".".$ext;
        $producto->lActivo = 1;
        if ($request->hasFile('img')) {
            $ext = $request->img->extension();
            $request->img->move(public_path('img/productos'), "producto_".$request->codigo_barras.".".$ext);
            $producto->img = "producto_".$request->codigo_barras.".".$ext;
        }

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
        $lstCategorias = Categorias::where('lActivo', 1)->get();
        $lstMateriales = Materiales::where('lActivo', 1)->get();
        $lstProveedores = Proveedores::where('lActivo', 1)->get();

        $producto = Producto::where('id', $id)->first();
        return view("productos.productos_edit", [
            "producto" => $producto,
            'lstCategorias' => $lstCategorias,
            'lstMateriales' => $lstMateriales,
            'lstProveedores' => $lstProveedores
        ]);
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
        $producto->id_categoria = $request->id_categoria;
        $producto->id_material = $request->id_material;
        $producto->id_proveedor = $request->id_proveedor;

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
            $lstProductos = array();

            $productos = Producto::query();

            switch($request->cTipoBusqueda){
                case 'T':
                    $productos->where('lActivo', 1);
                break;
                case 'B':
                    $productos->where('lActivo', 1)->where('existencia', '>', 4);
                break;
                case 'R':
                    $productos->where('lActivo', 1)->where('existencia', '<', 1);
                break;
                case 'N':
                    $productos->where('lActivo', 1)->where('existencia', '>', 0)->where('existencia', '<', 4);
                break;
                case 'F':
                    $productos->where('lActivo', 1)->where('img', NULL);
                break;
            }

            switch($request->cTipoBusquedaProveedor){
                case 'T':
                    $productos->where('lActivo', '>=', 0);
                break;
                case 0:
                    $productos->where('id_proveedor', 0);
                break;
                default:
                    $productos->where('id_proveedor', $request->cTipoBusquedaProveedor);
                break;
            }

            switch($request->cTipoBusquedaMaterial){
                case 'T':
                    $productos->where('id_material', '>=', 0);
                break;
                case 0:
                    $productos->where('id_material', 0);
                break;
                default:
                    $productos->where('id_material', $request->cTipoBusquedaMaterial);
                break;
            }

            switch($request->cTipoBusquedaCategoria){
                case 'T':
                    $productos->where('id_categoria', '>=', 0);
                break;
                case 0:
                    $productos->where('id_categoria', 0);
                break;
                default:
                    $productos->where('id_categoria', $request->cTipoBusquedaCategoria);
                break;
            }

            $productos = $productos->get(); 

            foreach($productos as $producto){
                $lstProductos[] = array (
                    "id" => $producto->id,
                    "codigo_barras" => $producto->codigo_barras,
                    "descripcion" => $producto->descripcion,
                    "precio_compra" => $producto->precio_compra,
                    "precio_venta" => $producto->precio_venta,
                    "existencia" => $producto->existencia,
                    "img" => $producto->img,
                    "lActivo" => $producto->lActivo,
                    "proveedor" => ($producto->id_proveedor == 0) ? "N/A" : $producto->Proveedores->cNombreProveedor,
                    "id_proveedor" => $producto->id_proveedor,
                    "material" => ($producto->id_material == 0) ? "N/A" : $producto->Materiales->cNombreMaterial,
                    "id_material" => $producto->id_material,
                    "categoria" => ($producto->id_categoria == 0) ? "N/A" : $producto->Categorias->cNombreCategoria,
                    "id_categoria" => $producto->id_categoria,
                    // "material" => $producto->codigo_barras,
                    // "categoria" => $producto->codigo_barras,
                );
            }

            return  $lstProductos;

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
