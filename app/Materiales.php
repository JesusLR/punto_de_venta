<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Materiales extends Model
{
    protected $table = "materiales";
    protected $fillable = ["cNombreMaterial", "cNotasMaterial". "lActivo"];
}
