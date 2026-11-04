<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddLegacyColumnToWhatsappMessages extends Migration
{
    public function up()
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->longText('whatsapp_messagescol')->nullable();
        });
    }
    public function down()
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropColumn('whatsapp_messagescol');
        });
    }
}
