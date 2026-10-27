<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Relación con los permisos
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Relación con los usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
