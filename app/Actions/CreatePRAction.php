<?php

namespace App\Actions;

use App\Models\PR;

// esto crea PR nuevos sin meter la logica de numeracion dentro del controlador, que seria bastante mas feo.
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
        $nextNumber = (PR::where('course_id', $courseId)->max('number') ?? 0) + 1;

        $pr = PR::create([
            'course_id' => $courseId,
            'number' => $nextNumber,
            'nombre' => 'Proyecto ' . $nextNumber,
            'fecha_limite' => null,
            'fase' => PR::DEFAULT_FASE,
        ]);

        return $pr;
    }
}
