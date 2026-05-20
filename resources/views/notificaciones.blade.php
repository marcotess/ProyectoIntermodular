@extends('layouts.dashboard')

@section('title', 'Notificaciones | Gestion de Cursos')
@section('activeNav', 'notificaciones')
@section('pageTitle', 'Notificaciones del usuario')
@section('pageSubtitle', 'Listado completo de avisos generados por el sistema para mantener el seguimiento de documentos, estados y cambios recientes.')

@section('content')
<div class="app-surface-strong rounded-[28px] p-4 sm:p-6">
    @php($displayTimezone = config('app.display_timezone', 'Europe/Madrid'))
    <div class="max-h-[720px] space-y-4 overflow-auto pr-1 user-scroll">
        @forelse($notificaciones as $notificacion)
            <article class="rounded-[24px] border {{ $notificacion->fecha_lectura ? 'border-[color:var(--line)] bg-white/70' : 'border-[rgba(124,45,60,0.18)] bg-[rgba(124,45,60,0.06)]' }} p-5 transition-colors">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex flex-wrap items-center gap-3">
                            <h2 class="text-[12px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $notificacion->tema }}</h2>
                            @if(!$notificacion->fecha_lectura)
                                <span class="rounded-full bg-[color:var(--accent)] px-3 py-2 text-[10px] font-bold uppercase tracking-[0.14em] text-white">Nueva</span>
                            @endif
                        </div>
                        <p class="text-[14px] leading-7 text-[color:var(--muted)]">{{ $notificacion->mensaje }}</p>
                        <p class="mt-4 text-[11px] uppercase tracking-[0.14em] text-[color:var(--muted)]">
                            Enviada {{ optional($notificacion->fecha_envio)?->timezone($displayTimezone)->format('d/m/Y H:i') ?? $notificacion->created_at->timezone($displayTimezone)->format('d/m/Y H:i') }}
                            @if($notificacion->fecha_lectura)
                                · Leida {{ $notificacion->fecha_lectura->timezone($displayTimezone)->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        @if($notificacion->link)
                            <a href="{{ route('notificaciones.open', $notificacion) }}" class="ui-button-soft inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.14em]">
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                Abrir elemento
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="flex h-[320px] items-center justify-center rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/55 text-[13px] uppercase tracking-[0.15em] text-[color:var(--muted)]">
                No tienes notificaciones.
            </div>
        @endforelse
    </div>
</div>
@endsection