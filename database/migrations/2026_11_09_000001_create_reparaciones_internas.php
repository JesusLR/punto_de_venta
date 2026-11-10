<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateReparacionesInternas extends Migration
{
    public function up()
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->uuid('solicitud_id')->unique();
            $table->string('tipo', 20)->default('INTERNA');
            $table->string('estado', 20)->default('EN_REPARACION')->index();
            $table->decimal('total', 12, 2);
            $table->text('observaciones')->nullable();
            $this->referenceColumn($table, 'id_usuario', 'users')->nullable();
            $this->referenceColumn($table, 'id_usuario_entrega', 'users')->nullable();
            $this->referenceColumn($table, 'id_egreso', 'egresos')->nullable()->unique();
            $table->timestamp('entregado_at')->nullable();
            $table->timestamps();
            $table->foreign('id_usuario')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_usuario_entrega')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_egreso')->references('id')->on('egresos');
        });
        Schema::create('reparacion_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reparacion_id');
            $this->referenceColumn($table, 'producto_id', 'productos');
            $table->string('codigo_barras');
            $table->string('descripcion');
            $table->unsignedInteger('cantidad');
            $table->timestamps();
            $table->foreign('reparacion_id')->references('id')->on('reparaciones');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->unique(['reparacion_id', 'producto_id']);
            $table->index('producto_id');
        });
        foreach ([
            'ver_reparaciones' => ['Ver Reparaciones', 'Permite consultar reparaciones internas y sus piezas.'],
            'manage_reparaciones' => ['Administrar Reparaciones', 'Permite enviar piezas a reparación y recibirlas con su egreso.'],
        ] as $slug => $permission) {
            DB::table('permissions')->updateOrInsert(['slug' => $slug], [
                'name' => $permission[0], 'description' => $permission[1], 'created_at' => now(), 'updated_at' => now(),
            ]);
            $adminId = DB::table('roles')->where('slug', 'admin')->value('id');
            if ($adminId) DB::table('permission_role')->updateOrInsert([
                'role_id' => $adminId, 'permission_id' => DB::table('permissions')->where('slug', $slug)->value('id'),
            ], []);
        }
    }

    private function referenceColumn(Blueprint $table, $name, $target)
    {
        // Historical MySQL installations have both signed and unsigned IDs.
        if (DB::connection()->getDriverName() !== 'mysql') return $table->unsignedBigInteger($name);
        $column = DB::selectOne(
            'SELECT DATA_TYPE AS data_type, COLUMN_TYPE AS column_type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [DB::connection()->getDatabaseName(), $target, 'id']
        );
        $types = ['bigint' => 'bigInteger', 'int' => 'integer', 'mediumint' => 'mediumInteger', 'smallint' => 'smallInteger', 'tinyint' => 'tinyInteger'];
        if (!$column || !isset($types[$column->data_type])) throw new RuntimeException('Tipo de identificador no compatible en '.$target.'.id');
        $definition = $table->{$types[$column->data_type]}($name);
        if (strpos($column->column_type, 'unsigned') !== false) $definition->unsigned();
        return $definition;
    }

    public function down()
    {
        // Stock movements and financial records must not be discarded by a rollback.
        if (DB::table('reparaciones')->exists()) {
            throw new RuntimeException('No se puede revertir Reparaciones mientras existan registros.');
        }
        Schema::dropIfExists('reparacion_productos');
        Schema::dropIfExists('reparaciones');
        $ids = DB::table('permissions')->whereIn('slug', ['ver_reparaciones', 'manage_reparaciones'])->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
}
