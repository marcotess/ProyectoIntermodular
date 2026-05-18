<?php

namespace App\Actions;

use App\Models\Course;

class CoursesAction
{

    // listar todos los cursoslistar todos los cursos existentes
    public function listarCursos()
    {
        return Course::all();
    }
}
