<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Relación con los roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
