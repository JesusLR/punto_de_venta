<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingLegacyColumns extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('img', 8000)->nullable();
            $table->boolean('lActivo')->nullable();
            $table->integer('id_proveedor')->nullable();
            $table->integer('id_categoria')->nullable();
            $table->integer('id_material')->nullable();
        });
        Schema::table('productos_vendidos', function (Blueprint $table) {
            $table->string('img', 50)->nullable();
            $table->integer('lActivo')->nullable();
            $table->integer('id_proveedor')->nullable();
            $table->integer('id_categoria')->nullable();
            $table->integer('id_material')->nullable();
        });
        Schema::table('ventas', function (Blueprint $table) {
            $table->longText('cNombreVenta')->nullable();
            $table->integer('id_usuario')->nullable();
        });
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre_apartado', 1000)->nullable();
        });
        Schema::table('apartados', function (Blueprint $table) {
            $table->string('nombre_apartado', 1000)->nullable();
        });
    }

    public function down()
    {
        foreach (['apartados', 'clientes'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn('nombre_apartado');
            });
        }
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['cNombreVenta', 'id_usuario']);
        });
        foreach (['productos_vendidos', 'productos'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropColumn(['img', 'lActivo', 'id_proveedor', 'id_categoria', 'id_material']);
            });
        }
    }
}

