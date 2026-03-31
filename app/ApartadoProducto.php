<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartadoProducto extends Model
{
    protected $table = 'apartado_productos';

    protected $fillable = [
        'id_apartado',
        'id_producto',
        'descripcion',
        'codigo_barras',
        'precio',
        'cantidad',
    ];

    public function apartado()
    {
        return $this->belongsTo(Apartado::class, 'id_apartado');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
