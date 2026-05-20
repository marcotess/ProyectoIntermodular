<?php

namespace App\Actions;

use App\Models\Course;

class CoursesAction
{
    /**
     * Recupera el listado completo de cursos registrados.
     */
    public function listarCursos()
    {
        return Course::all();
    }
}
