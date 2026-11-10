<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddReparacionesExternas extends Migration
{
    public function up()
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('contacto_usuario_id')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->decimal('precio_compra', 12, 2)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $this->incomeReference($table)->nullable()->unique();
            $table->foreign('contacto_usuario_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_ingreso')->references('id')->on('ingresos');
        });
        Schema::create('reparacion_imagenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reparacion_id');
            $table->string('ruta');
            $table->timestamps();
            $table->foreign('reparacion_id')->references('id')->on('reparaciones');
        });
    }

    private function incomeReference(Blueprint $table)
    {
        if (DB::connection()->getDriverName() !== 'mysql') return $table->unsignedBigInteger('id_ingreso');
        $column = DB::selectOne('SELECT DATA_TYPE AS data_type, COLUMN_TYPE AS column_type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?', [DB::connection()->getDatabaseName(), 'ingresos', 'id']);
        $types = ['bigint' => 'bigInteger', 'int' => 'integer', 'mediumint' => 'mediumInteger', 'smallint' => 'smallInteger', 'tinyint' => 'tinyInteger'];
        if (!$column || !isset($types[$column->data_type])) throw new RuntimeException('Identificador no compatible en ingresos.id');
        $definition = $table->{$types[$column->data_type]}('id_ingreso');
        if (strpos($column->column_type, 'unsigned') !== false) $definition->unsigned();
        return $definition;
    }

    public function down()
    {
        if (DB::table('reparaciones')->where('tipo', 'EXTERNA')->exists()) throw new RuntimeException('No se puede revertir mientras existan reparaciones externas.');
        Schema::dropIfExists('reparacion_imagenes');
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropForeign(['contacto_usuario_id']); $table->dropForeign(['id_ingreso']);
            $table->dropColumn(['contacto_usuario_id', 'contacto_nombre', 'contacto_telefono', 'precio_compra', 'precio_venta', 'id_ingreso']);
        });
    }
}
