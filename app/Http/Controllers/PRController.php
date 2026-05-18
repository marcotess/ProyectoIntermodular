<?php

namespace App\Http\Controllers;

use App\Models\PR;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PRController extends Controller
{
    private function formatPr(PR $pr): array
    {
        return [
            'id' => $pr->id,
            'nombre' => 'PR ' . $pr->number,
            'numero' => $pr->number,
            'fase' => $pr->fase,
            'fecha_limite' => $pr->fecha_limite,
            'teachers' => $pr->teachers->map(fn ($teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
            ])->values()->all(),
            'documentos_count' => $pr->documents->count(),
        ];
    }

    private function resolveAccessibleCourse(int $courseId): array
    {
        $user = Auth::user();
        $course = Course::with(['prs.documents.reviewers', 'prs.teachers'])->findOrFail($courseId);

        abort_unless($user->canAccessCourse($course), 403);

        $prs = $course->prs->sortByDesc('number')->values();

        if (!$user->hasRole('gestor') && !$user->canAccessCourseAsTeacher($course)) {
            $prs = $prs->filter(function (PR $pr) use ($user) {
                return $user->reviewedDocuments()->where('pr_id', $pr->id)->exists();
            })->values();
        }

        return [$course, $prs, $user];
    }

    public function view(Request $request, $courseId)
    {
        [$course, $prs, $user] = $this->resolveAccessibleCourse((int) $courseId);
        $canManagePrs = $user->hasRole('gestor');
        $canEditPr = $user->hasAnyRole(['gestor', 'revisor']);

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            // Se expone el mismo contexto de la vista como payload JSON para el frontend.
            return response()->json([
                'course' => [
                    'id' => $course->id,
                    'codigo' => $course->code,
                    'nombre' => $course->name,
                ],
                'prs' => $prs->map(fn (PR $pr) => $this->formatPr($pr))->values()->all(),
                'can_manage_prs' => $canManagePrs,
                'can_edit_pr' => $canEditPr,
            ]);
        }

        return view('PR', compact('course', 'prs', 'canManagePrs', 'canEditPr'));
    }

    public function index(Request $request, $courseId)
    {
        [$course, $prs, $user] = $this->resolveAccessibleCourse((int) $courseId);
        $canManagePrs = $user->hasRole('gestor');
        $canEditPr = $user->hasAnyRole(['gestor', 'revisor']);

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            // Para listados la API devuelve solo la colección serializada de PRs.
            return response()->json($prs->map(fn (PR $pr) => $this->formatPr($pr))->values()->all());
        }

        return view('PR', compact('course', 'prs', 'canManagePrs', 'canEditPr'));
    }

    public function create(Request $request, $courseId)
    {
        $pr = app(\App\Actions\CreatePRAction::class)->execute($courseId);

        return response()->json(['success' => (bool) $pr, 'pr_id' => $pr->id]);
    }

    public function cambiarFase(Request $request, $prId)
    {
        $validated = $request->validate([
            'fase' => ['required', 'string', Rule::in(PR::PHASES)],
        ]);

        $result = app(\App\Actions\PRsAction::class)->cambiarFase($prId, $validated['fase']);

        return response()->json(['success' => $result]);
    }
}
