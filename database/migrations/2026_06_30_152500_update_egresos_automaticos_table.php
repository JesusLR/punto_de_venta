<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEgresosAutomaticosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First drop the foreign key on egresos table to avoid constraints issues
        Schema::table('egresos', function (Blueprint $table) {
            $table->dropForeign(['id_egreso_automatico']);
        });

        // Drop the old table
        Schema::dropIfExists('egresos_automaticos');

        // Create the updated table
        Schema::create('egresos_automaticos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 150);
            $table->decimal('monto', 12, 2);
            $table->string('frecuencia', 20)->default('MENSUAL'); // 'MENSUAL' or 'SEMANAL'
            $table->integer('dia_mes')->nullable(); // 1 to 31
            $table->integer('dia_semana')->nullable(); // 1 to 7 (1 = Monday, 7 = Sunday)
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Restore foreign key on egresos table
        Schema::table('egresos', function (Blueprint $table) {
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
        });

        Schema::dropIfExists('egresos_automaticos');

        Schema::create('egresos_automaticos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 150);
            $table->decimal('monto', 12, 2);
            $table->integer('dia_mes');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::table('egresos', function (Blueprint $table) {
            $table->foreign('id_egreso_automatico')->references('id')->on('egresos_automaticos')->onDelete('set null');
        });
    }
}
