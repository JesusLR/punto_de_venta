<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relación con el Rol del usuario
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Comprobar si el usuario tiene un permiso específico
     */
    public function hasPermission($permissionSlug)
    {
        // El usuario con ID 1 siempre es super-administrador
        if ($this->id === 1) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        // Si el rol es administrador, tiene todos los permisos
        if ($this->role->slug === 'admin') {
            return true;
        }

        return $this->role->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Obtener lista plana de slugs de todos los permisos asignados
     */
    public function getAllPermissions()
    {
        if ($this->id === 1 || ($this->role && $this->role->slug === 'admin')) {
            return Permission::pluck('slug')->toArray();
        }

        if (!$this->role) {
            return [];
        }

        return $this->role->permissions->pluck('slug')->toArray();
    }
}
