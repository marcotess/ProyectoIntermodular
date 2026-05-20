@extends('layouts.dashboard')

@section('title', 'Tareas | Gestion de Cursos')
@section('activeNav', 'tasks')
@section('pageTitle', 'Tareas')
@section('pageSubtitle', 'Esta seccion queda visible dentro de la navegacion principal y preparada para el desarrollo posterior del modulo.')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
    <section class="app-surface-strong rounded-[28px] p-6 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Modulo en desarrollo</p>
        <h2 class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">La seccion de tareas ya tiene su espacio.</h2>
        <p class="mt-4 max-w-2xl text-[15px] leading-8 text-[color:var(--muted)]">Por ahora se muestra como placeholder visual dentro del nuevo sistema de navegacion. Esto permite mantener una estructura estable mientras el modulo funcional se desarrolla en una fase posterior.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('profile') }}" class="ui-button-primary inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Volver al perfil</a>
            <a href="{{ route('courses.index') }}" class="ui-button-soft inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Ir a cursos</a>
        </div>
    </section>

    <aside class="app-surface-strong rounded-[28px] p-6 sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Proxima iteracion</p>
        <div class="mt-5 space-y-4">
            <article class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                <h3 class="text-[15px] font-bold text-[color:var(--ink)]">Pendientes previstos</h3>
                <p class="mt-2 text-[14px] leading-7 text-[color:var(--muted)]">Listado de tareas, filtros por estado, responsables y prioridad, asi como un seguimiento temporal por usuario o curso.</p>
            </article>
            <article class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                <h3 class="text-[15px] font-bold text-[color:var(--ink)]">Objetivo visual</h3>
                <p class="mt-2 text-[14px] leading-7 text-[color:var(--muted)]">Mantener la misma identidad de la plataforma para que futuras ampliaciones no rompan la coherencia del producto.</p>
            </article>
        </div>
    </aside>
</div>
@endsection