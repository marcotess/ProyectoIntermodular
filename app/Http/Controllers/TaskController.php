<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\PR;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

// aqui saco tareas con una prioridad entendible, para que no toque mirar fechas una por una.
class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

        $teacherTasks = $user->taughtPrs()
            ->with('course')
            ->whereNotNull('fecha_limite')
            ->get()
            ->map(function (PR $pr) use ($now) {
                $deadline = Carbon::parse($pr->fecha_limite);

                return [
                    'type' => 'pr',
                    'title' => 'PR ' . $pr->number,
                    'subtitle' => ($pr->course?->code ? $pr->course->code . ' · ' : '') . ($pr->course?->name ?? 'Curso no disponible'),
                    'detail' => 'Asignado como docente',
                    'deadline_at' => $deadline,
                    'days_remaining' => $now->diffInDays($deadline, false),
                    'route' => route('pr.documentos.index', ['pr' => $pr->id]),
                    'route_label' => 'Abrir PR',
                    'badge' => 'PR',
                ];
            });

        $reviewerTasks = $user->reviewedDocuments()
            ->with(['pr.course', 'latestVariant.status'])
            ->get()
            ->map(function (Document $document) use ($now) {
                $deadlineTarget = $document->latestVariant?->deadline_target;

                if (! $deadlineTarget) {
                    return null;
                }

                $deadline = Carbon::parse($deadlineTarget);
                $courseLabel = $document->pr?->course?->code ? $document->pr->course->code . ' · ' . $document->pr->course->name : ($document->pr?->course?->name ?? 'Curso no disponible');

                return [
                    'type' => 'document',
                    'title' => $document->short_title,
                    'subtitle' => $courseLabel,
                    'detail' => 'Asignado como revisor · PR ' . ($document->pr?->number ?? '-'),
                    'deadline_at' => $deadline,
                    'days_remaining' => $now->diffInDays($deadline, false),
                    'route' => route('pr.documentos.index', ['pr' => $document->pr_id]) . '#document-' . $document->id,
                    'route_label' => 'Abrir documento',
                    'badge' => 'Documento',
                ];
            })
            ->filter();

        $tasks = collect()
            ->concat($teacherTasks)
            ->concat($reviewerTasks)
            ->sortBy(fn (array $task) => $task['deadline_at']->timestamp)
            ->values();

        $summary = $this->buildSummary($tasks, $now);

        return view('tareas', compact('tasks', 'summary'));
    }

    private function buildSummary(Collection $tasks, Carbon $now): array
    {
        return [
            'total' => $tasks->count(),
            'overdue' => $tasks->filter(fn (array $task) => $task['days_remaining'] < 0)->count(),
            'today' => $tasks->filter(fn (array $task) => $task['deadline_at']->isSameDay($now))->count(),
            'next_seven_days' => $tasks->filter(fn (array $task) => $task['days_remaining'] >= 0 && $task['days_remaining'] <= 7)->count(),
        ];
    }
}
