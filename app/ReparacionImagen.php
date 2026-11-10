<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReparacionImagen extends Model
{
    protected $table = 'reparacion_imagenes';
    protected $guarded = ['id'];
    public function reparacion() { return $this->belongsTo(Reparacion::class); }
}
