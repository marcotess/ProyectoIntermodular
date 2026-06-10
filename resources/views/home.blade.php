@extends('layouts.dashboard')

@section('title', 'Cursos | Gestion de Cursos')
@section('activeNav', 'courses')
@section('pageTitle', 'Cursos accesibles')
@section('pageSubtitle', 'Consulta los cursos disponibles para tu perfil y accede desde aqui al seguimiento de proyectos y documentos.')

@section('content')
<div class="grid gap-6">
    <div class="grid gap-4 md:grid-cols-3">
        <article class="app-surface-strong rounded-[24px] p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Total cursos</p>
            <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $courses->count() }}</p>
        </article>
        <article class="app-surface-strong rounded-[24px] p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Con proyectos</p>
            <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $courses->filter(fn ($course) => $course->prs->isNotEmpty())->count() }}</p>
        </article>
        <article class="app-surface-strong rounded-[24px] p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Sin proyectos</p>
            <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $courses->filter(fn ($course) => $course->prs->isEmpty())->count() }}</p>
        </article>
    </div>

    <div class="app-surface-strong overflow-hidden rounded-[28px] border border-[color:var(--line)]">
        <div class="overflow-x-auto user-scroll">
            <table class="page-table min-w-full border-collapse text-left text-[13px]">
                <thead>
                    <tr class="border-b border-[color:var(--line)]">
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Curso</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Ultimo proyecto</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Fecha limite</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Docentes</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Actualizado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        @php
                            $lastPr = $course->prs->first();
                            $teachers = $lastPr ? $lastPr->teachers->pluck('name')->map(fn ($name) => explode(' ', $name)[0]) : collect();
                        @endphp
                        <tr class="border-b border-[color:var(--line)] last:border-b-0">
                            <td class="px-5 py-4">
                                <a href="{{ route('courses.pr.view', $course->id) }}" class="block">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">{{ $course->code }}</p>
                                    <p class="mt-1 text-[15px] font-bold text-[color:var(--ink)]">{{ $course->name }}</p>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-semibold">{{ $lastPr ? 'Proyecto ' . $lastPr->number : '-' }}</td>
                            <td class="px-5 py-4 text-[color:var(--muted)]">{{ $lastPr && $lastPr->fecha_limite ? $lastPr->fecha_limite : 'Sin fecha' }}</td>
                            <td class="px-5 py-4 text-[color:var(--muted)]">{{ $teachers->implode(', ') ?: 'Sin docentes' }}</td>
                            <td class="px-5 py-4 text-[color:var(--muted)]">{{ $lastPr && $lastPr->updated_at ? $lastPr->updated_at->format('d/m/Y') : 'Sin actividad' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-[14px] text-[color:var(--muted)]">No tienes cursos asignados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
