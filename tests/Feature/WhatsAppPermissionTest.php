<?php

namespace Tests\Feature;

use App\User;
use App\Role;
use App\Permission;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WhatsAppPermissionTest extends TestCase
{
    public function test_whatsapp_routes_reject_users_without_permission()
    {
        $user = new User;
        $user->forceFill(['id' => 99, 'name' => 'Sin permisos']);
        $user->setRelation('role', null);
        $this->actingAs($user);
        foreach ($this->app['router']->getRoutes() as $route) {
            if (strpos($route->uri(), 'whatsapp/') !== 0) continue;
            $uri = preg_replace('/\{[^}]+\}/', 'test-session', $route->uri());
            $this->json($route->methods()[0], '/'.$uri)->assertStatus(403);
        }
    }

    public function test_permission_allows_access_through_authorization_middleware()
    {
        $user = new User;
        $user->forceFill(['id' => 99]);
        $role = new Role;
        $role->slug = 'operador';
        $role->setRelation('permissions', collect([new Permission(['slug' => 'manage_whatsapp'])]));
        $user->setRelation('role', $role);
        $this->actingAs($user);
        $response = (new \App\Http\Middleware\CheckPermission)->handle(
            \Illuminate\Http\Request::create('/whatsapp/settings'),
            function () { return response('allowed'); }, 'manage_whatsapp'
        );
        $this->assertSame('allowed', $response->getContent());
    }

    public function test_migration_assigns_only_admin_and_can_be_repeated_and_reversed()
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        require_once database_path('migrations/2014_10_12_000000_create_users_table.php');
        require_once database_path('migrations/2026_06_22_000001_create_permissions_system_tables.php');
        require_once database_path('migrations/2026_11_04_000003_add_manage_whatsapp_permission.php');
        (new \CreateUsersTable)->up();
        (new \CreatePermissionsSystemTables)->up();
        $migration = new \AddManageWhatsappPermission;
        $migration->up();
        $migration->up();
        $id = DB::table('permissions')->where('slug', 'manage_whatsapp')->value('id');
        $this->assertSame(1, DB::table('permission_role')->where('permission_id', $id)->count());
        $admin = DB::table('roles')->where('slug', 'admin')->value('id');
        $this->assertSame((int) $admin, (int) DB::table('permission_role')->where('permission_id', $id)->value('role_id'));
        $migration->down();
        $this->assertSame(0, DB::table('permissions')->where('slug', 'manage_whatsapp')->count());
        $this->assertGreaterThan(0, DB::table('permissions')->count());
    }
}
