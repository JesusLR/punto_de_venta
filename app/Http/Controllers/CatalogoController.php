<?php

namespace App\Http\Controllers;
use App\Categorias;
use App\Materiales;
use App\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(){

        $lstProducto = array();
        $lstCategorias = Categorias::where('lActivo', 1)->get();
        $lstMateriales = Materiales::where('lActivo', 1)->get();
        foreach($lstCategorias as $categoria){
            foreach($lstMateriales as $material){
                $producto = Producto::where('id_categoria', $categoria->id)
                                    ->where('id_material', $material->id)
                                    ->where('lActivo', 1)
                                    ->where('existencia', '>', 0)
                                    ->orderBy('id', 'desc')
                                    ->first();
                if(!is_null($producto)){
                    $lstProducto[] = array(
                        'id' => $producto->id,
                        'id_categoria' => $producto->id_categoria,
                        'id_material' => $producto->id_material,
                        'img' => $producto->img,
                    );
                }
            }
        }
        // dd($lstProducto);
        $lstProducto = json_encode($lstProducto);
        $lstProducto = json_decode($lstProducto);
        return view("catalogo.catalogo_index", [
            'lstCategorias' => $lstCategorias,
            'lstMateriales' => $lstMateriales,
            'lstProducto' =>  $lstProducto
        ]);
        // return view("catalogo.catalogo_index");
    }

    public function verCategoria($id_categoria, $id_material){
        try{
            $lstProducto = Producto::where('id_categoria', $id_categoria)
            ->where('id_material', $id_material)
            ->where('lActivo', 1)
            ->where('existencia', '>', 0)
            ->orderBy('id', 'desc')
            ->select('productos.*')
            ->get();
            return response()->json([
                'lSuccess' => true,
                'lstProducto' => $lstProducto,
            ]);
        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }
}
