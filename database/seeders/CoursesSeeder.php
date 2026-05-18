<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        // Borra todos los cursos
        \DB::table('courses')->truncate();

        // Deja solo 4 cursos
        $courses = [
            ['code' => '003', 'name' => 'REVIT_I'],
            ['code' => '001', 'name' => 'ARCHICAD_I'],
            ['code' => '031', 'name' => 'TELE_AGRICOLA'],
            ['code' => '065', 'name' => 'AUTOCAD'],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(['code' => $course['code']], ['name' => $course['name']]);
        }
    }
}
