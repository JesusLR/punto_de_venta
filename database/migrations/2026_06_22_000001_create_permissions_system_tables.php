<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePermissionsSystemTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Crear tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Crear tabla de permisos
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 3. Crear tabla pivote de roles y permisos
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            
            $table->primary(['role_id', 'permission_id']);
        });

        // 4. Agregar columna role_id a la tabla de usuarios
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('password');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });

        // 5. Sembrar roles
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Administrador',
            'slug' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cajeroRoleId = DB::table('roles')->insertGetId([
            'name' => 'Cajero / Vendedor',
            'slug' => 'cajero',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Sembrar permisos
        $permissions = [
            // Catálogos
            ['name' => 'Ver Categorías', 'slug' => 'ver_categorias', 'description' => 'Permite ver el listado de categorías.'],
            ['name' => 'Crear/Editar Categorías', 'slug' => 'manage_categories', 'description' => 'Permite agregar y editar categorías.'],
            ['name' => 'Eliminar Categorías', 'slug' => 'delete_categories', 'description' => 'Permite eliminar categorías.'],

            ['name' => 'Ver Clientes', 'slug' => 'ver_clientes', 'description' => 'Permite ver el listado de clientes.'],
            ['name' => 'Crear/Editar Clientes', 'slug' => 'manage_clients', 'description' => 'Permite agregar y editar clientes.'],
            ['name' => 'Eliminar Clientes', 'slug' => 'delete_clients', 'description' => 'Permite eliminar clientes.'],

            ['name' => 'Ver Materiales', 'slug' => 'ver_materiales', 'description' => 'Permite ver el listado de materiales.'],
            ['name' => 'Crear/Editar Materiales', 'slug' => 'manage_materials', 'description' => 'Permite agregar y editar materiales.'],
            ['name' => 'Eliminar Materiales', 'slug' => 'delete_materials', 'description' => 'Permite eliminar materiales.'],

            ['name' => 'Ver Proveedores', 'slug' => 'ver_proveedores', 'description' => 'Permite ver el listado de proveedores.'],
            ['name' => 'Crear/Editar Proveedores', 'slug' => 'manage_suppliers', 'description' => 'Permite agregar y editar proveedores.'],
            ['name' => 'Eliminar Proveedores', 'slug' => 'delete_suppliers', 'description' => 'Permite eliminar proveedores.'],

            ['name' => 'Ver Usuarios', 'slug' => 'ver_usuarios', 'description' => 'Permite ver el listado de usuarios del sistema.'],
            ['name' => 'Crear/Editar/Eliminar Usuarios', 'slug' => 'manage_users', 'description' => 'Permite administrar los usuarios del sistema.'],

            // Productos
            ['name' => 'Ver Productos', 'slug' => 'ver_productos', 'description' => 'Permite ver el catálogo de productos.'],
            ['name' => 'Crear/Editar Productos', 'slug' => 'manage_products', 'description' => 'Permite agregar y editar productos.'],
            ['name' => 'Eliminar Productos', 'slug' => 'delete_products', 'description' => 'Permite eliminar productos.'],
            ['name' => 'Ver Costo y Utilidad', 'slug' => 'view_cost_and_utility', 'description' => 'Permite ver el precio de compra y la utilidad de los productos.'],

            // Tienda
            ['name' => 'Vender', 'slug' => 'make_sales', 'description' => 'Permite realizar ventas en la caja.'],
            
            ['name' => 'Ver Apartados', 'slug' => 'ver_apartados', 'description' => 'Permite ver la lista de apartados.'],
            ['name' => 'Administrar Apartados', 'slug' => 'manage_apartados', 'description' => 'Permite abonar, agregar productos, cancelar o ejecutar apartados.'],

            ['name' => 'Ver Ventas', 'slug' => 'view_sales', 'description' => 'Permite ver el historial de ventas realizadas.'],
            ['name' => 'Administrar Finanzas', 'slug' => 'manage_finances', 'description' => 'Permite ver finanzas y egresos/gastos.'],

            // Estadísticas
            ['name' => 'Ver Estadísticas', 'slug' => 'view_statistics', 'description' => 'Permite ver gráficas de ventas y estadísticas de productos.'],

            // Roles & Permisos
            ['name' => 'Administrar Roles y Permisos', 'slug' => 'manage_roles', 'description' => 'Permite administrar los roles y asignar permisos en el sistema.']
        ];

        $permissionIds = [];
        foreach ($permissions as $p) {
            $p['created_at'] = now();
            $p['updated_at'] = now();
            $permissionIds[$p['slug']] = DB::table('permissions')->insertGetId($p);
        }

        // 7. Asociar permisos a roles
        // Admin tiene todos los permisos
        foreach ($permissionIds as $slug => $id) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $id,
            ]);
        }

        // Cajero tiene permisos limitados
        $cajeroPermissions = [
            'ver_categorias',
            'ver_clientes',
            'manage_clients',
            'ver_materiales',
            'ver_proveedores',
            'ver_productos',
            'make_sales',
            'ver_apartados',
            'manage_apartados',
            'view_sales',
        ];

        foreach ($cajeroPermissions as $slug) {
            if (isset($permissionIds[$slug])) {
                DB::table('permission_role')->insert([
                    'role_id' => $cajeroRoleId,
                    'permission_id' => $permissionIds[$slug],
                ]);
            }
        }

        // 8. Asignar roles a usuarios existentes
        // El usuario con ID 1 es Admin
        DB::table('users')->where('id', 1)->update(['role_id' => $adminRoleId]);

        // Todos los demás usuarios son Cajeros
        DB::table('users')->where('id', '!=', 1)->update(['role_id' => $cajeroRoleId]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
}
