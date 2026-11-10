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
            'imagenes_adicionales' => 'nullable|array|max:10',
            'imagenes_adicionales.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048|dimensions:max_width=4000,max_height=4000',
            'eliminar_imagenes' => 'nullable|array|max:10',
            'eliminar_imagenes.*' => 'required|string|max:255|distinct',
            'quitar_imagen_principal' => 'sometimes|boolean',
        ];
        foreach (['id_categoria' => 'categorias', 'id_material' => 'materiales', 'id_proveedor' => 'proveedores'] as $field => $table) {
            $rules[$field] = 'required|integer|min:0'.($request->input($field) != 0 ? '|exists:'.$table.',id' : '');
        }
        $data = $request->validate($rules);
        $nuevas = []; $eliminadas = [];
        try {
            $producto = DB::transaction(function () use ($producto, $request, $data, &$nuevas, &$eliminadas) {
                if ($producto->exists) $producto = Producto::whereKey($producto->id)->lockForUpdate()->firstOrFail();
                $quitar = $data['eliminar_imagenes'] ?? [];
                if (count($quitar)) {
                    $imagenes = $producto->imagenesAdicionales()->whereIn('archivo', $quitar)->get();
                    if (!$producto->exists || $imagenes->count() !== count($quitar)) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['eliminar_imagenes' => 'Una imagen no pertenece a este producto. Recarga la página.']);
                    }
                    foreach ($imagenes as $imagen) { $eliminadas[] = $imagen->archivo; $imagen->delete(); }
                }
                $archivos = $request->file('imagenes_adicionales', []);
                if (count($archivos) && $producto->imagenesAdicionales()->count() + count($archivos) > 10) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['imagenes_adicionales' => 'El producto admite hasta 10 imágenes adicionales.']);
                }
                $producto->fill(collect($data)->except(['img', 'imagenes_adicionales', 'eliminar_imagenes', 'quitar_imagen_principal'])->all());
                if ($request->boolean('quitar_imagen_principal')) {
                    if ($producto->img) $eliminadas[] = $producto->img;
                    $producto->img = null;
                }
                if ($request->hasFile('img')) {
                    $nombre = 'producto_'.Str::uuid().'.'.$request->file('img')->extension();
                    $request->file('img')->move(public_path('img/productos'), $nombre);
                    $nuevas[] = $nombre; $producto->img = $nombre;
                }
                $producto->save();
                foreach ($archivos as $archivo) {
                    $nombre = 'producto_'.Str::uuid().'.'.$archivo->extension();
                    $archivo->move(public_path('img/productos'), $nombre); $nuevas[] = $nombre;
                    $producto->imagenesAdicionales()->create(['archivo' => $nombre]);
                }
                return $producto;
            });
        } catch (\Throwable $exception) {
            foreach ($nuevas as $imagen) @unlink(public_path('img/productos/'.$imagen));
            throw $exception;
        }
        foreach ($eliminadas as $archivo) {
            // Legacy files can also be referenced by another piece or the landing page.
            if (basename($archivo) !== $archivo || Producto::where('img', $archivo)->exists() || \App\ProductoImagen::where('archivo', $archivo)->exists()) continue;
            if (\Illuminate\Support\Facades\Schema::hasTable('homepage_settings') && \App\HomepageSetting::where('value', 'like', '%'.$archivo.'%')->exists()) continue;
            @unlink(public_path('img/productos/'.$archivo));
        }
        return response()->json(['lSuccess' => true, 'id' => $producto->id]);
    }
}
