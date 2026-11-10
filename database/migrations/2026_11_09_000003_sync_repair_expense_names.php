<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SyncRepairExpenseNames extends Migration
{
    public function up()
    {
        DB::table('reparaciones')->whereNotNull('id_egreso')->whereNotNull('nombre')->where('nombre', '<>', '')
            ->orderBy('id')->chunkById(100, function ($reparaciones) {
                foreach ($reparaciones as $reparacion) {
                    if (trim($reparacion->nombre) !== '') {
                        DB::table('egresos')->where('id', $reparacion->id_egreso)->update(['concepto' => trim($reparacion->nombre)]);
                    }
                }
            });
    }

    public function down()
    {
        // Preserve user-assigned names when rolling back this data reconciliation.
    }
}
