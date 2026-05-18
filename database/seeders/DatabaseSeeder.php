<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {

        $this->call([
            RolesSeeder::class,
            AdminUserSeeder::class,
            CoursesSeeder::class,
            DocentesSeeder::class,
            PRsSeeder::class,
            PRTeachersSeeder::class,
            PRDocumentsSeeder::class,
        ]);

    }
}
