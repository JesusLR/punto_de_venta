<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apartado extends Model
{
    protected $table = 'apartados';

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'total',
        'total_abonado',
        'saldo',
        'estado',
        'id_venta',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function productos()
    {
        return $this->hasMany(ApartadoProducto::class, 'id_apartado');
    }

    public function abonos()
    {
        return $this->hasMany(ApartadoAbono::class, 'id_apartado');
    }
}
