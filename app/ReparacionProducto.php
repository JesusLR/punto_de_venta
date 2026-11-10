<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReparacionProducto extends Model
{
    protected $table = 'reparacion_productos';
    protected $guarded = ['id'];
    public function reparacion() { return $this->belongsTo(Reparacion::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
