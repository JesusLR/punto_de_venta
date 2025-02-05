<?php

namespace App\Http\Controllers;

use App\Proveedores;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\importProductos;

class ProveedoresController extends Controller
{
    public function index(){
        return view('proveedores.proveedores_index');
    }

    public function gridProveedores(Request $request){
        try{
            $proveedores = Proveedores::where('lActivo', 1)->get();
            return  $proveedores;

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    
    }
    public function saveProveedores(Request $request){
        try{
            $cMensaje = "";
            if($request->cNombreProveedor == null || $request->cNombreProveedor == ""){
                throw  new Exception('Ingrese un proveedor');
            }

            $existeProveedor = Proveedores::where('cNombreProveedor', trim($request->cNombreProveedor))->where('id', '!=', $request->id)->where('lActivo', 1)->count();

            if($existeProveedor > 0){
                throw  new Exception('Esta proveedor ya se encuentra registrado.');
            }

            if($request->id == 0){
                $proveedor = new Proveedores;
                $cMensaje = "Proveedor guardado con exito!";
            }else{
                $proveedor = Proveedores::where('id', $request->id)->first();
                $cMensaje = "Proveedor actualizado con exito!";
            }

            $proveedor->cNombreProveedor = strtoupper($request->cNombreProveedor);
            $proveedor->cNotasProveedor = ($request->cNotasProveedor == null) ? "" : $request->cNotasProveedor;
            $proveedor->lActivo = 1;
            $proveedor->save();

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

    public function getProveedor($id){
        try{

            $proveedor = Proveedores::where('id', $id)->first();

            return response()->json([
                'lSuccess' => true,
                'cData' => $proveedor,
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

    public function deleteProveedor(Request $request){
        try{

            $proveedor = Proveedores::where('id', $request->id)->first();
            $proveedor->lActivo = 0;
            $proveedor->save();

            return response()->json([
                'lSuccess' => true,
                'cMensaje' => "Proveedor eliminado con exito!",
            ]);

        }catch(Exception $ex){
            return response()->json([
                'lSuccess' => false,
                'cMensaje' => $ex->getMessage(),
            ]);
        }
    }

}
