<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // roles por ahora
        Role::updateOrCreate(['name' => 'gestor']);
        Role::updateOrCreate(['name' => 'docente']);
        Role::updateOrCreate(['name' => 'revisor']);

    }
}
