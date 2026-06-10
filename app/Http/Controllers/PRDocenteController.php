<?php

namespace App\Http\Controllers;

use App\Models\PR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PRDocenteController extends Controller
{
    private function resolveAccessiblePr(int $prId): PR
    {
        $pr = PR::with(['course', 'documents.reviewers', 'teachers'])->findOrFail($prId);

        abort_unless(Auth::user()->canAccessPr($pr), 403);

        return $pr;
    }

    // Update fecha límite of PR
    public function updateFechaLimite(Request $request, $prId)
    {
        $pr = $this->resolveAccessiblePr((int) $prId);
        $fechaLimite = $request->input('fecha_limite');
        $result = app(\App\Actions\PRsAction::class)->updateFechaLimite($pr->id, $fechaLimite);
        return response()->json(['success' => $result]);
    }

    // Add docentes to PR
    public function add(Request $request, $prId)
    {
        $pr = $this->resolveAccessiblePr((int) $prId);
        $docenteIds = $request->input('docentes');
        $result = app(\App\Actions\PRsAction::class)->addDocentesToPR($pr->id, $docenteIds);

        return response()->json(['success' => $result]);
    }

    // Remove docente from PR
    public function remove(Request $request, $prId, $docenteId)
    {
        $pr = $this->resolveAccessiblePr((int) $prId);
        $result = app(\App\Actions\PRsAction::class)->removeDocenteFromPR($pr->id, $docenteId);

        return response()->json(['success' => $result]);
    }
}
