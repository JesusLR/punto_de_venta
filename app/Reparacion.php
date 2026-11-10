<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    protected $table = 'reparaciones';
    protected $guarded = ['id'];
    protected $casts = ['total' => 'decimal:2', 'precio_compra' => 'decimal:2', 'precio_venta' => 'decimal:2', 'entregado_at' => 'datetime', 'cancelado_at' => 'datetime'];

    public function piezas() { return $this->hasMany(ReparacionProducto::class, 'reparacion_id'); }
    public function usuario() { return $this->belongsTo(User::class, 'id_usuario'); }
    public function receptor() { return $this->belongsTo(User::class, 'id_usuario_entrega'); }
    public function cancelador() { return $this->belongsTo(User::class, 'id_usuario_cancelacion'); }
    public function egreso() { return $this->belongsTo(Egreso::class, 'id_egreso'); }
    public function ingreso() { return $this->belongsTo(Ingreso::class, 'id_ingreso'); }
    public function cliente() { return $this->belongsTo(Cliente::class, 'contacto_cliente_id'); }
    public function imagenes() { return $this->hasMany(ReparacionImagen::class); }
    public function conceptoEgreso() { return trim((string) $this->nombre) ?: 'Reparación '.($this->tipo === 'EXTERNA' ? 'externa' : 'interna').' #'.$this->id; }
    public function accesiblePor(User $user)
    {
        return $user->hasPermission('ver_todas_reparaciones') || (int) $this->id_usuario === (int) $user->id;
    }

    public function scopeVisiblePara($query, User $user)
    {
        if (!$user->hasPermission('ver_todas_reparaciones')) $query->where('id_usuario', $user->id);
        return $query;
    }
}
