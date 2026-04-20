<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Egreso extends Model
{
    protected $table = 'egresos';

    protected $fillable = [
        'id_usuario',
        'concepto',
        'monto',
        'fecha',
        'observaciones',
    ];

    protected $dates = [
        'fecha',
        'created_at',
        'updated_at',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
