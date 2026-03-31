<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoPagoToApartadoAbonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartado_abonos', function (Blueprint $table) {
            $table->string('tipo_pago', 30)->default('EFECTIVO')->after('monto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apartado_abonos', function (Blueprint $table) {
            $table->dropColumn('tipo_pago');
        });
    }
}
