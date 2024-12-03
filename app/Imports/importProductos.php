<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use App\Producto;  // El modelo donde se guardarán los datos
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
            $producto =  new Producto();
            $producto->codigo_barras = $row['codigo_barras'];
            $producto->descripcion = $row['descripcion'];
            $producto->precio_compra = $row['precio_compra'];
            $producto->precio_venta = $row['precio_venta'];
            $producto->existencia = $row['existencia'];
            $producto->lActivo = 1;
            $producto->save();

            return $producto;
        }
    // }
}
