<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DisableWhatsappAutomaticReplies extends Migration
{
    public function up()
    {
        if (DB::table('homepage_settings')->where('key', 'wa_bot_enabled')->exists()) {
            DB::table('homepage_settings')->where('key', 'wa_bot_enabled')->update(['value' => '0', 'updated_at' => now()]);
        } else {
            DB::table('homepage_settings')->insert(['key' => 'wa_bot_enabled', 'value' => '0', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down()
    {
        // No reactivar respuestas automáticas al revertir una instalación.
    }
}
