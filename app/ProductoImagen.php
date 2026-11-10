<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagenes';
    protected $fillable = ['archivo'];
    public function producto() { return $this->belongsTo(Producto::class); }
}
