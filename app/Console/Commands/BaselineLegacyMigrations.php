<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BaselineLegacyMigrations extends Command
{
    protected $signature = 'migrate:baseline-legacy {--apply : Registrar la estructura existente sin ejecutar las migraciones}';
    protected $description = 'Verifica tablas y columnas historicas antes de conciliar el historial de migraciones';

    public function handle()
    {
        if (!Schema::hasTable('migrations')) {
            $this->error('No existe el historial. Para una base nueva utiliza migrate.');
            return 1;
        }

        $manifest = json_decode(ltrim(file_get_contents(database_path('legacy-schema.json')), "\xEF\xBB\xBF"), true);
        $ran = DB::table('migrations')->pluck('migration')->all();
        $pending = [];
        $errors = [];
        foreach ($manifest as $migration => $tables) {
            if (in_array($migration, $ran, true)) {
                continue;
            }
            foreach ($tables as $table => $columns) {
                if (!Schema::hasTable($table)) {
                    $errors[] = "$migration: falta tabla $table";
                    continue;
                }
                foreach ($columns as $column) {
                    // La base historica usa este nombre como clave primaria.
                    if ($table === 'apartado_productos' && $column === 'id'
                        && Schema::hasColumn($table, 'apartado_productos')) {
                        continue;
                    }
                    if (!Schema::hasColumn($table, $column)) {
                        $errors[] = "$migration: falta $table.$column";
                    }
                }
            }
            $pending[] = $migration;
        }

        foreach (['admin', 'cajero'] as $slug) {
            if (!Schema::hasTable('roles') || !DB::table('roles')->where('slug', $slug)->exists()) {
                $errors[] = "Falta el rol historico $slug";
            }
        }
        foreach (['manage_homepage', 'manage_general_settings'] as $slug) {
            if (!Schema::hasTable('permissions') || !DB::table('permissions')->where('slug', $slug)->exists()) {
                $errors[] = "Falta el permiso historico $slug";
            }
        }
        if ($errors) {
            foreach ($errors as $error) {
                $this->error($error);
            }
            $this->error('No se modifico el historial. Revisa las diferencias antes de migrar.');
            return 1;
        }

        foreach ($pending as $migration) {
            $this->line($migration);
        }
        if (!$this->option('apply')) {
            $this->info('Solo verificacion: '.count($pending).' migraciones existentes. Usa --apply para registrarlas.');
            return 0;
        }

        DB::transaction(function () use ($pending) {
            foreach ($pending as $migration) {
                if (!DB::table('migrations')->where('migration', $migration)->exists()) {
                    DB::table('migrations')->insert(['migration' => $migration, 'batch' => 0]);
                }
            }
        });
        $this->info('Historial conciliado. Solo se insertaron registros en migrations; no se ejecutaron up() ni seeders.');
        return 0;
    }
}
