<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFechaAbonoToApartadoAbonosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apartado_abonos', function (Blueprint $table) {
            $table->dateTime('fecha_abono')->nullable()->after('tipo_pago');
        });

        DB::table('apartado_abonos')->update([
            'fecha_abono' => DB::raw('created_at')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apartado_abonos', function (Blueprint $table) {
            $table->dropColumn('fecha_abono');
        });
    }
}