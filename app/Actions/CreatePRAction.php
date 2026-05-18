<?php

namespace App\Actions;

use App\Models\PR;

class CreatePRAction
{
    /**
     * Crea un nuevo PR para un curso dado
     *
     * @param  int  $courseId
     * @return PR
     */
    public function execute($courseId)
    {
        $pr = PR::create([
            'course_id' => $courseId,
            'number' => PR::where('course_id', $courseId)->max('number') + 1,
            'fecha_limite' => null,
            'fase' => PR::DEFAULT_FASE,
        ]);

        return $pr;
    }
}
