<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdEgresoAutomaticoToEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('egresos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_egreso_automatico')->nullable()->after('id_usuario');
            $table->foreign('id_egreso_automatico')->references('id')->on('egresos_automaticos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('egresos', function (Blueprint $table) {
            $table->dropForeign(['id_egreso_automatico']);
            $table->dropColumn('id_egreso_automatico');
        });
    }
}
