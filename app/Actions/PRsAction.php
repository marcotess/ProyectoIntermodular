<?php

namespace App\Actions;

use App\Models\PR;

class PRsAction
{
    /**
     *   actualizala fecha limite de un PR
     */
    public function updateFechaLimite($prId, $fechaLimite)
    {
        $pr = PR::findOrFail($prId);
        $pr->fecha_limite = $fechaLimite;
        $pr->save();

        return true;
    }

    /**
     * Cambia la fase de un PR.
     */
    public function cambiarFase($prId, $fase)
    {
        $pr = PR::findOrFail($prId);
        $pr->fase = $fase;
        $pr->save();

        return true;
    }

    /**
     *  Añade docentes a un PR
     */
    public function addDocentesToPR($prId, $docenteIds)
    {
        $pr = PR::findOrFail($prId);
        $pr->teachers()->syncWithoutDetaching($docenteIds);

        return true;
    }

    /**
     *              Quita un docente de un PR
     */
    public function removeDocenteFromPR($prId, $docenteId)
    {
        $pr = PR::findOrFail($prId);
        $pr->teachers()->detach($docenteId);

        return true;
    }
}
