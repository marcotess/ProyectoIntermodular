<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\PR;
use Illuminate\Database\Seeder;

class PRsSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::whereIn('code', ['003', '001', '031', '065'])->get();
        $prData = [
            ['course_code' => '003', 'numbers' => [1, 2]],
            ['course_code' => '001', 'numbers' => [1]],
            ['course_code' => '031', 'numbers' => [1]],
            ['course_code' => '065', 'numbers' => [1, 2]],
        ];

        foreach ($prData as $data) {
            $course = $courses->where('code', $data['course_code'])->first();
            foreach ($data['numbers'] as $number) {
                PR::create([
                    'course_id' => $course->id,
                    'number' => $number,
                    'fase' => PR::DEFAULT_FASE,
                    'deadline' => now()->addDays(rand(5, 20)),
                    'fecha_limite' => now()->addDays(rand(21, 40))->format('Y-m-d'),
                ]);
            }
        }
    }
}
