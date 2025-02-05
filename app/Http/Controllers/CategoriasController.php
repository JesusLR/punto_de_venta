<?php

namespace App\Http\Controllers;

use App\Categorias;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\importProductos;

class CategoriasController extends Controller
{
    public function index(){
        return view('categorias.categorias_index');
    }

    public function gridCategorias(Request $request){
        try{
            $categorias = Categorias::where('lActivo', 1)->get();
            return  $categorias;

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    
    }
    public function saveCategorias(Request $request){
        try{
            $cMensaje = "";
            if($request->cNombreCategoria == null || $request->cNombreCategoria == ""){
                throw  new Exception('Ingrese una categoria');
            }

            $existeCategoria = Categorias::where('cNombreCategoria', trim($request->cNombreCategoria))->where('id', '!=', $request->id)->where('lActivo', 1)->count();

            if($existeCategoria > 0){
                throw  new Exception('Esta categoria ya se encuentra registrada.');
            }

            if($request->id == 0){
                $categoria = new Categorias;
                $cMensaje = "Categoria guardada con exito!";
            }else{
                $categoria = Categorias::where('id', $request->id)->first();
                $cMensaje = "Categoria actualizada con exito!";
            }

            $categoria->cNombreCategoria = strtoupper($request->cNombreCategoria);
            $categoria->cNotasCategoria = ($request->cNotasCategoria == null) ? "" : $request->cNotasCategoria;
            $categoria->lActivo = 1;
            $categoria->save();

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => $cMensaje
            ]);


        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function getCategoria($id){
        try{

            $categoria = Categorias::where('id', $id)->first();

            return response()->json([
                'lSuccess' => true,
                'cData' => $categoria,
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function deleteCategoria(Request $request){
        try{

            $categoria = Categorias::where('id', $request->id)->first();
            $categoria->lActivo = 0;
            $categoria->save();

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => "Categoria eliminada con exito!",
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }
}
