<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreciosMaterialesTable extends Migration
{
    public function up()
    {
        Schema::create('precios_materiales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_material')->nullable()->default(0);
            $table->longText('json')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('precios_materiales');
    }
}
