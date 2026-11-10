<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEtapaToReparacionImagenes extends Migration
{
    public function up()
    {
        Schema::table('reparacion_imagenes', function (Blueprint $table) {
            $table->string('etapa', 12)->default('ANTES');
            $table->index(['reparacion_id', 'etapa']);
        });
    }

    public function down()
    {
        Schema::table('reparacion_imagenes', function (Blueprint $table) {
            $table->dropIndex(['reparacion_id', 'etapa']); $table->dropColumn('etapa');
        });
    }
}
