<?php

namespace App\Http\Controllers;

use App\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductoModalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage_products']);
    }

    public function store(Request $request)
    {
        $producto = new Producto;
        $producto->lActivo = 1;
        return $this->guardar($request, $producto);
    }

    public function update(Request $request, Producto $producto)
    {
        return $this->guardar($request, $producto);
    }

    private function guardar(Request $request, Producto $producto)
    {
        $rules = [
            'codigo_barras' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'precio_compra' => 'required|numeric|min:0|max:9999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'precio_venta' => 'required|numeric|min:0|max:9999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'existencia' => 'required|numeric|min:0|max:9999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'img' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ];
        foreach (['id_categoria' => 'categorias', 'id_material' => 'materiales', 'id_proveedor' => 'proveedores'] as $field => $table) {
            $rules[$field] = 'required|integer|min:0'.($request->input($field) != 0 ? '|exists:'.$table.',id' : '');
        }
        $data = $request->validate($rules);
        $producto->fill(collect($data)->except('img')->all());
        $newImage = null;
        if ($request->hasFile('img')) {
            $newImage = 'producto_'.Str::uuid().'.'.$request->file('img')->extension();
            $request->file('img')->move(public_path('img/productos'), $newImage);
            $producto->img = $newImage;
        }
        try {
            DB::transaction(function () use ($producto) { $producto->save(); });
        } catch (\Throwable $exception) {
            if ($newImage) @unlink(public_path('img/productos/'.$newImage));
            throw $exception;
        }
        return response()->json(['lSuccess' => true, 'id' => $producto->id]);
    }
}
