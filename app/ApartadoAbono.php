<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartadoAbono extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($abono) {
            \DB::table('notificaciones_leidas')
                ->where('notification_type', 'apartado_inactivo')
                ->where('notification_id', $abono->id_apartado)
                ->delete();
        });

        static::deleted(function ($abono) {
            \DB::table('notificaciones_leidas')
                ->where('notification_type', 'apartado_inactivo')
                ->where('notification_id', $abono->id_apartado)
                ->delete();
        });
    }

    protected $table = 'apartado_abonos';

    protected $dates = [
        'created_at',
        'updated_at',
        'fecha_abono',
    ];

    protected $fillable = [
        'id_apartado',
        'id_usuario',
        'monto',
        'tipo_pago',
        'observaciones',
        'fecha_abono',
    ];

    public function getFechaRegistroAttribute()
    {
        return $this->fecha_abono ?: $this->created_at;
    }

    public function apartado()
    {
        return $this->belongsTo(Apartado::class, 'id_apartado');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
