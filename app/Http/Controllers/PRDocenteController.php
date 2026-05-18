<?php

namespace App\Http\Controllers;

use App\Models\PR;
use Illuminate\Http\Request;

class PRDocenteController extends Controller
{
    // Update fecha límite of PR
    public function updateFechaLimite(Request $request, $prId)
    {
        $fechaLimite = $request->input('fecha_limite');
        $result = app(\App\Actions\PRsAction::class)->updateFechaLimite($prId, $fechaLimite);

        return response()->json(['success' => $result]);
    }

    // Add docentes to PR
    public function add(Request $request, $prId)
    {
        $docenteIds = $request->input('docentes');
        $result = app(\App\Actions\PRsAction::class)->addDocentesToPR($prId, $docenteIds);

        return response()->json(['success' => $result]);
    }

    // Remove docente from PR
    public function remove(Request $request, $prId, $docenteId)
    {
        $result = app(\App\Actions\PRsAction::class)->removeDocenteFromPR($prId, $docenteId);

        return response()->json(['success' => $result]);
    }
}
