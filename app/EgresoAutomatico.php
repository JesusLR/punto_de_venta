<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EgresoAutomatico extends Model
{
    protected $table = 'egresos_automaticos';

    protected $fillable = [
        'concepto',
        'monto',
        'frecuencia',
        'dia_mes',
        'dia_semana',
        'observaciones',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function egresos()
    {
        return $this->hasMany(Egreso::class, 'id_egreso_automatico');
    }
}
