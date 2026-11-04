<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdministratorUserSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $this->call(AdministratorRoleSeeder::class);
            $user = User::firstOrNew(['email' => 'administrador@colibri.com']);

            if (!$user->exists) {
                $password = env('ADMIN_PASSWORD') ?: Str::random(24);
                $user->name = 'Administrador';
                $user->password = Hash::make($password);
            }

            $user->role_id = Role::where('slug', 'admin')->firstOrFail()->id;
            $user->save();

            if ($user->wasRecentlyCreated && $this->command && !env('ADMIN_PASSWORD')) {
                $this->command->info('Contraseña inicial de administrador@colibri.com: '.$password);
            }
        });
    }
}
