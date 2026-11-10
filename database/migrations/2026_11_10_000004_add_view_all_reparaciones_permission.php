<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddViewAllReparacionesPermission extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            DB::table('permissions')->updateOrInsert(['slug' => 'ver_todas_reparaciones'], [
                'name' => 'Ver reparaciones de todos los usuarios',
                'description' => 'Permite consultar reparaciones registradas por otros usuarios y filtrar por responsable.',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $id = DB::table('permissions')->where('slug', 'ver_todas_reparaciones')->value('id');
            $admin = DB::table('roles')->where('slug', 'admin')->value('id');
            if ($admin) DB::table('permission_role')->updateOrInsert(['role_id' => $admin, 'permission_id' => $id], []);
        });
    }

    public function down()
    {
        $ids = DB::table('permissions')->where('slug', 'ver_todas_reparaciones')->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
}
