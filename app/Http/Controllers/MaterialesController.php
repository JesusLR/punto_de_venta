<?php

namespace App\Http\Controllers;

use App\Materiales;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\importProductos;

class MaterialesController extends Controller
{
    public function index(){
        return view('materiales.materiales_index');
    }

    public function gridMateriales(Request $request){
        try{
            $materiales = Materiales::where('lActivo', 1)->get();
            return  $materiales;

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    
    }
    public function saveMateriales(Request $request){
        try{
            $cMensaje = "";
            if($request->cNombreMaterial == null || $request->cNombreMaterial == ""){
                throw  new Exception('Ingrese un material');
            }

            $existeMaterial = Materiales::where('cNombreMaterial', trim($request->cNombreMaterial))->where('id', '!=', $request->id)->where('lActivo', 1)->count();

            if($existeMaterial > 0){
                throw  new Exception('Este material ya se encuentra registrado.');
            }

            if($request->id == 0){
                $materiales = new Materiales;
                $cMensaje = "Material guardado con exito!";
            }else{
                $materiales = Materiales::where('id', $request->id)->first();
                $cMensaje = "Material actualizado con exito!";
            }

            $materiales->cNombreMaterial = strtoupper($request->cNombreMaterial);
            $materiales->cNotasMaterial = ($request->cNotasMaterial == null) ? "" : $request->cNotasMaterial;
            $materiales->lActivo = 1;
            $materiales->save();

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

    public function getMaterial($id){
        try{

            $materiales = Materiales::where('id', $id)->first();

            return response()->json([
                'lSuccess' => true,
                'cData' => $materiales,
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function deleteMaterial(Request $request){
        try{

            $materiales = Materiales::where('id', $request->id)->first();
            $materiales->lActivo = 0;
            $materiales->save();

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => "Material eliminada con exito!",
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }
}
