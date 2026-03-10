<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrecioMateriales extends Model
{
   protected $table = "precios_materiales";
    protected $fillable = ["id_material", "json"];
}
