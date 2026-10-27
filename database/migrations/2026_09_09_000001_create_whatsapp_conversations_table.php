<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('chat_id')->unique();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->string('step')->default('start');
            $table->string('last_keyword')->nullable();
            $table->text('context_data')->nullable();
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
}
