<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartadosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_abonado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->string('estado', 20)->default('ABIERTO');
            $table->unsignedBigInteger('id_venta')->nullable();
            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_venta')->references('id')->on('ventas')->onUpdate('cascade')->onDelete('set null');
        });

        Schema::create('apartado_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_apartado');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->string('descripcion');
            $table->string('codigo_barras');
            $table->decimal('precio', 12, 2);
            $table->decimal('cantidad', 12, 2);
            $table->timestamps();

            $table->foreign('id_apartado')->references('id')->on('apartados')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('productos')->onUpdate('cascade')->onDelete('set null');
        });

        Schema::create('apartado_abonos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_apartado');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->decimal('monto', 12, 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('id_apartado')->references('id')->on('apartados')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apartado_abonos');
        Schema::dropIfExists('apartado_productos');
        Schema::dropIfExists('apartados');
    }
}
