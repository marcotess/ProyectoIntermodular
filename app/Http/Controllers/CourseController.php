<?php

namespace App\Http\Controllers;

class CourseController extends Controller
{
    public function index(CoursesAction $listCoursesAction)
    {
        $courses = $listCoursesAction->listarCursos();
        return view('courses.index', compact('courses'));
    }
}
