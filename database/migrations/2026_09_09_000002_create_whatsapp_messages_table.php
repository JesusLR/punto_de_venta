<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversation_id')->index();
            $table->string('chat_id')->index();
            $table->string('direction')->default('inbound'); // inbound (cliente), outbound (tienda/bot)
            $table->string('sender_name')->nullable();
            $table->text('body');
            $table->string('type')->default('text'); // text, document, image
            $table->string('status')->default('sent');
            $table->timestamps();

            $table->foreign('conversation_id')
                ->references('id')
                ->on('whatsapp_conversations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_messages');
    }
}
