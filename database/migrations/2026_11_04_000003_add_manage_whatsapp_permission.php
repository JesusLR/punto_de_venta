<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddManageWhatsappPermission extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            DB::table('permissions')->updateOrInsert(['slug' => 'manage_whatsapp'], [
                'name' => 'Administrar WhatsApp',
                'description' => 'Permite configurar WhatsApp, administrar sesiones, consultar QR y gestionar conversaciones.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $permissionId = DB::table('permissions')->where('slug', 'manage_whatsapp')->value('id');
            $adminId = DB::table('roles')->where('slug', 'admin')->value('id');
            if ($adminId) {
                DB::table('permission_role')->updateOrInsert([
                    'role_id' => $adminId,
                    'permission_id' => $permissionId,
                ], []);
            }
        });
    }

    public function down()
    {
        DB::transaction(function () {
            $permissionId = DB::table('permissions')->where('slug', 'manage_whatsapp')->value('id');
            if ($permissionId) {
                DB::table('permission_role')->where('permission_id', $permissionId)->delete();
                DB::table('permissions')->where('id', $permissionId)->delete();
            }
        });
    }
}
