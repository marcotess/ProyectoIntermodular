@extends('layouts.dashboard')

@section('title', 'Tareas | Gestion de Cursos')
@section('activeNav', 'tasks')
@section('pageTitle', 'Tareas')
@section('pageSubtitle', 'Listado priorizado de proyectos y documentos con fecha limite cercana para tus asignaciones como docente o revisor.')

@section('content')
@php
    $firstTask = $tasks->first();
@endphp
<div class="grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
    <section class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="app-surface-strong rounded-[24px] p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tareas con fecha</p>
                <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $summary['total'] }}</p>
            </article>
            <article class="app-surface-strong rounded-[24px] p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Vencidas</p>
                <p class="mt-3 text-3xl font-extrabold text-[color:var(--danger)]">{{ $summary['overdue'] }}</p>
            </article>
            <article class="app-surface-strong rounded-[24px] p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Para hoy</p>
                <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $summary['today'] }}</p>
            </article>
            <article class="app-surface-strong rounded-[24px] p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Próximas 7 días</p>
                <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $summary['next_seven_days'] }}</p>
            </article>
        </div>

        <div aria-labelledby="tasks-priority-title" class="app-surface-strong rounded-[28px] p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4 border-b border-[color:var(--line)] pb-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Orden de urgencia</p>
                    <h2 id="tasks-priority-title" class="mt-2 text-2xl font-extrabold text-[color:var(--ink)]">Proyectos y documentos asignados</h2>
                </div>
                <span class="rounded-full border border-[color:var(--line)] bg-white/80 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">Más cercanos primero</span>
            </div>

            <div class="mt-5 space-y-4">
                @forelse($tasks as $task)
                    @php
                        $daysRemaining = $task['days_remaining'];
                        $deadlineTone = $daysRemaining < 0
                            ? 'text-[color:var(--danger)]'
                            : ($daysRemaining <= 3 ? 'text-[color:var(--accent-strong)]' : 'text-[color:var(--muted)]');
                        $deadlineLabel = $daysRemaining < 0
                            ? 'Vencida hace ' . abs($daysRemaining) . ' día(s)'
                            : ($daysRemaining === 0 ? 'Vence hoy' : 'Quedan ' . $daysRemaining . ' día(s)');
                    @endphp
                    <article class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5 transition hover:border-[color:var(--line-strong)] hover:bg-white">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-[color:var(--accent-soft)] px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $task['badge'] }}</span>
                                    <span class="text-[11px] font-semibold uppercase tracking-[0.16em] {{ $deadlineTone }}">{{ $deadlineLabel }}</span>
                                </div>
                                <h3 class="mt-3 text-[18px] font-extrabold text-[color:var(--ink)]">{{ $task['title'] }}</h3>
                                <p class="mt-2 text-[14px] text-[color:var(--muted)]">{{ $task['subtitle'] }}</p>
                                <p class="mt-1 text-[13px] uppercase tracking-[0.12em] text-[color:var(--muted)]">{{ $task['detail'] }}</p>
                            </div>
                            <div class="min-w-[220px] rounded-[22px] border border-[color:var(--line)] bg-[color:var(--accent-soft)]/60 px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--muted)]">Fecha límite</p>
                                <p class="mt-2 text-[18px] font-extrabold text-[color:var(--ink)]">{{ $task['deadline_at']->format('d/m/Y H:i') }}</p>
                                <a href="{{ $task['route'] }}" class="ui-button-primary mt-4 inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">{{ $task['route_label'] }}</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/60 px-5 py-10 text-center text-[14px] text-[color:var(--muted)]">No tienes proyectos ni documentos asignados con fecha límite informada.</div>
                @endforelse
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="app-surface-strong rounded-[28px] p-6 sm:p-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Siguiente vencimiento</p>
            @if($firstTask)
                @php
                    $firstTaskDays = $firstTask['days_remaining'];
                @endphp
                <h2 class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $firstTask['title'] }}</h2>
                <p class="mt-4 text-[15px] leading-8 text-[color:var(--muted)]">{{ $firstTask['subtitle'] }}. {{ $firstTask['detail'] }}.</p>
                <div class="mt-6 rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Fecha límite</p>
                    <p class="mt-3 text-2xl font-extrabold text-[color:var(--ink)]">{{ $firstTask['deadline_at']->format('d/m/Y H:i') }}</p>
                    <p class="mt-2 text-[14px] text-[color:var(--muted)]">
                        @if($firstTaskDays < 0)
                            Vencida hace {{ abs($firstTaskDays) }} día(s).
                        @elseif($firstTaskDays === 0)
                            Vence hoy.
                        @else
                            Quedan {{ $firstTaskDays }} día(s).
                        @endif
                    </p>
                    <a href="{{ $firstTask['route'] }}" class="ui-button-primary mt-5 inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Ir al elemento</a>
                </div>
            @else
                <h2 class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">Sin tareas urgentes</h2>
                <p class="mt-4 text-[15px] leading-8 text-[color:var(--muted)]">Cuando un proyecto docente o un documento revisado tenga fecha límite, aparecerá aquí ordenado por cercanía.</p>
            @endif
        </section>
    </aside>
</div>
@endsection