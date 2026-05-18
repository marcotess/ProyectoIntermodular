<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PRTeachersSeeder extends Seeder
{
    public function run()
    {
        $docentes = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'docente');
        })->get();

        $prs = \App\Models\PR::all();

        foreach ($prs as $pr) {
            $numDocentes = rand(1, min(4, $docentes->count()));
            $docentesAleatorios = $docentes->random($numDocentes);
            foreach ($docentesAleatorios as $docente) {
                \App\Models\PRTeacher::firstOrCreate([
                    'pr_id' => $pr->id,
                    'user_id' => $docente->id,
                ]);
            }
        }
    }
}
