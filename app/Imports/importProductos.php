<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Producto;
use App\Proveedores;
use App\Materiales;
use App\Categorias;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Storage;

class importProductos implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    // public function collection(Collection $collection)
    // {
        public function model(array $row){

            $proveedor = Proveedores::where('cNombreProveedor', trim($row['proveedor']))->where('lActivo', 1)->first();
            $material = Materiales::where('cNombreMaterial', trim($row['material']))->where('lActivo', 1)->first();
            $categoria = Categorias::where('cNombreCategoria', trim($row['categoria']))->where('lActivo', 1)->first();

            $producto =  new Producto();
            $producto->codigo_barras = $row['codigo_barras'];
            $producto->descripcion = $row['descripcion'];
            $producto->precio_compra = $row['precio_compra'];
            $producto->precio_venta = $row['precio_venta'];
            $producto->existencia = $row['existencia'];
            $producto->id_proveedor = (is_null($proveedor)) ? 0 : $proveedor->id;
            $producto->id_material = (is_null($material)) ? 0 : $material->id;
            $producto->id_categoria = (is_null($categoria)) ? 0 : $categoria->id;
            $producto->lActivo = 1;
            $producto->save();

            return $producto;
        }
    // }
}
