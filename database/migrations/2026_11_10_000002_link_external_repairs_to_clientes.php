<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LinkExternalRepairsToClientes extends Migration
{
    public function up()
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $type = 'bigInteger'; $unsigned = true;
            if (DB::connection()->getDriverName() === 'mysql') {
                $column = DB::selectOne('SELECT DATA_TYPE AS data_type, COLUMN_TYPE AS column_type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?', [DB::connection()->getDatabaseName(), 'clientes', 'id']);
                $types = ['bigint' => 'bigInteger', 'int' => 'integer', 'mediumint' => 'mediumInteger', 'smallint' => 'smallInteger', 'tinyint' => 'tinyInteger'];
                if (!$column || !isset($types[$column->data_type])) throw new RuntimeException('Identificador no compatible en clientes.id');
                $type = $types[$column->data_type]; $unsigned = strpos($column->column_type, 'unsigned') !== false;
            }
            $definition = $table->{$type}('contacto_cliente_id')->nullable();
            if ($unsigned) $definition->unsigned();
            $table->foreign('contacto_cliente_id')->references('id')->on('clientes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reparaciones', function (Blueprint $table) { $table->dropForeign(['contacto_cliente_id']); $table->dropColumn('contacto_cliente_id'); });
    }
}
