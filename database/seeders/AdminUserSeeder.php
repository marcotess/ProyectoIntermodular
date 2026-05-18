<?php


namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {

        // gestor
        $user = User::create([
            'name' => 'Gestor',
            'email' => 'gestor@demo.com',
            'password' => Hash::make('123456'),
        ]);

        $role = Role::where('name', 'gestor')->first();
        $user->roles()->attach($role);

        // docente
        $user = User::create([
            'name' => 'Docente',
            'email' => 'docente@demo.com',
            'password' => Hash::make('123456'),
        ]);

        $role = Role::where('name', 'docente')->first();
        $user->roles()->attach($role);

        // revisor
        $user = User::create([
            'name' => 'Revisor',
            'email' => 'revisor@demo.com',
            'password' => Hash::make('123456'),
        ]);

        $role = Role::where('name', 'revisor')->first();
        $user->roles()->attach($role);

    }
}
