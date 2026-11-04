<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class AdministratorRoleSeeder extends Seeder
{
    public function run()
    {
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrador']);
        $role->permissions()->syncWithoutDetaching(Permission::pluck('id')->all());
    }
}
