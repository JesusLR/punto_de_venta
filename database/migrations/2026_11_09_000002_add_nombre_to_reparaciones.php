<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreToReparaciones extends Migration
{
    public function up()
    {
        Schema::table('reparaciones', function (Blueprint $table) { $table->string('nombre', 150)->nullable(); });
    }

    public function down()
    {
        Schema::table('reparaciones', function (Blueprint $table) { $table->dropColumn('nombre'); });
    }
}
