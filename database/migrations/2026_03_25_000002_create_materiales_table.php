<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialesTable extends Migration
{
    public function up()
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('cNombreMaterial')->nullable();
            $table->longText('cNotasMaterial')->nullable();
            $table->boolean('lActivo')->nullable();
            $table->boolean('lActivoConsulta')->nullable();
            $table->longText('cSimbolo')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('materiales');
    }
}
