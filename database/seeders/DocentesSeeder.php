<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DocentesSeeder extends Seeder
{
    public function run()
    {
        $docenteRole = Role::firstOrCreate(['name' => 'docente']);
        $defaultPassword = Hash::make('docente123');

        $users = [
            ['name' => 'Ana Docente', 'email' => 'ana.docente@example.com', 'password' => $defaultPassword],
            ['name' => 'Luis Docente', 'email' => 'luis.docente@example.com', 'password' => $defaultPassword],
            ['name' => 'Marta Docente', 'email' => 'marta.docente@example.com', 'password' => $defaultPassword],
            ['name' => 'Pedro Docente', 'email' => 'pedro.docente@example.com', 'password' => $defaultPassword],
            ['name' => 'Marcos Prado', 'email' => 'marcos06prado@gmail.com', 'password' => $defaultPassword],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );

            $user->roles()->syncWithoutDetaching([$docenteRole->id]);
        }
    }
}
