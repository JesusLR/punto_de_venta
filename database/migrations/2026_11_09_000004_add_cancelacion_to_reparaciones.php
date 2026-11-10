<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelacionToReparaciones extends Migration
{
    public function up()
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->timestamp('cancelado_at')->nullable();
            $table->unsignedBigInteger('id_usuario_cancelacion')->nullable();
            $table->foreign('id_usuario_cancelacion')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_cancelacion']);
            $table->dropColumn(['cancelado_at', 'id_usuario_cancelacion']);
        });
    }
}
