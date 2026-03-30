<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartadoAbono extends Model
{
    protected $table = 'apartado_abonos';

    protected $fillable = [
        'id_apartado',
        'id_usuario',
        'monto',
        'tipo_pago',
        'observaciones',
    ];

    public function apartado()
    {
        return $this->belongsTo(Apartado::class, 'id_apartado');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
