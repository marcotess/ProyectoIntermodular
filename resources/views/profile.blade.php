@extends('layouts.dashboard')

@section('title', 'Perfil | Gestion de Cursos')
@section('activeNav', 'profile')
@section('pageTitle', 'Perfil del usuario')
@section('pageSubtitle', 'Vista principal tras iniciar sesion. Reune informacion del usuario, cursos accesibles y notificaciones recientes en una sola pantalla.')

@section('pageActions')
    <button type="button" data-panel-toggle="profile-settings-panel" aria-controls="profile-settings-panel" aria-expanded="{{ session('settings_status') || session('password_status') || $errors->settings->any() || $errors->passwordUpdate->any() ? 'true' : 'false' }}" class="ui-button-primary inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">
        <span class="material-symbols-outlined text-[18px]">settings</span>
        Ajustes
    </button>
    <form method="POST" action="{{ route('logout') }}" class="inline-flex">
        @csrf
        <button type="submit" class="ui-button-soft inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            Cerrar sesion
        </button>
    </form>
@endsection

@section('content')
@php
    $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $showSettingsPanel = session('settings_status') || session('password_status') || $errors->settings->any() || $errors->passwordUpdate->any();
    $showQuickNotifications = (bool) $user->show_quick_notifications;
@endphp
<div id="profile-settings-panel" role="region" aria-label="Panel de ajustes del perfil" aria-hidden="{{ $showSettingsPanel ? 'false' : 'true' }}" class="{{ $showSettingsPanel ? '' : 'hidden' }} app-surface-strong mb-6 rounded-[28px] p-6 sm:p-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Ajustes</p>
            <h2 class="mt-2 text-2xl font-extrabold text-[color:var(--ink)]">Preferencias y seguridad</h2>
            <p class="mt-2 text-[14px] text-[color:var(--muted)]">Controla correos, tema, densidad de la interfaz, paneles visibles y la contrasena de acceso.</p>
        </div>
        <button type="button" data-panel-hide="profile-settings-panel" aria-label="Cerrar ajustes" class="rounded-full border border-[color:var(--line)] bg-white p-2 text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]" title="Cerrar ajustes">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Preferencias</p>
            <h3 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Interfaz y avisos</h3>

            @if(session('settings_status'))
                <div role="status" class="mt-5 rounded-2xl border border-[rgba(47,111,89,0.22)] bg-[rgba(47,111,89,0.08)] px-4 py-3 text-[13px] text-[color:var(--success)]">{{ session('settings_status') }}</div>
            @endif

            @if($errors->settings->any())
                <div role="alert" class="mt-5 rounded-2xl border border-[rgba(160,63,81,0.22)] bg-[rgba(160,63,81,0.08)] px-4 py-3 text-[13px] text-[color:var(--danger)]">{{ $errors->settings->first() }}</div>
            @endif

            <form method="POST" action="{{ route('profile.settings.update') }}" class="mt-5 space-y-5">
                @csrf

                <div>
                    <label for="theme_preference" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tema</label>
                    <select id="theme_preference" name="theme_preference" class="ui-select w-full rounded-2xl px-4 py-3 text-[14px]">
                        <option value="light" @selected(old('theme_preference', $user->theme_preference) === 'light')>Claro</option>
                        <option value="dark" @selected(old('theme_preference', $user->theme_preference) === 'dark')>Oscuro</option>
                    </select>
                </div>

                <label class="flex items-start gap-3 rounded-[22px] border border-[color:var(--line)] bg-white/80 px-4 py-4">
                    <input type="hidden" name="receive_notification_emails" value="0" />
                    <input type="checkbox" name="receive_notification_emails" value="1" @checked(old('receive_notification_emails', $user->receive_notification_emails)) class="mt-1 h-4 w-4 rounded border-[color:var(--line-strong)] text-[color:var(--accent)]" />
                    <span>
                        <span class="block text-[13px] font-bold text-[color:var(--ink)]">Recibir correos de notificaciones</span>
                        <span class="mt-1 block text-[13px] leading-6 text-[color:var(--muted)]">Activa o silencia los emails automáticos cuando cambian los documentos que te afectan.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-[22px] border border-[color:var(--line)] bg-white/80 px-4 py-4">
                    <input type="hidden" name="compact_tables" value="0" />
                    <input type="checkbox" name="compact_tables" value="1" @checked(old('compact_tables', $user->compact_tables)) class="mt-1 h-4 w-4 rounded border-[color:var(--line-strong)] text-[color:var(--accent)]" />
                    <span>
                        <span class="block text-[13px] font-bold text-[color:var(--ink)]">Tablas compactas</span>
                        <span class="mt-1 block text-[13px] leading-6 text-[color:var(--muted)]">Reduce el padding de listados largos para ver más filas a la vez.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-[22px] border border-[color:var(--line)] bg-white/80 px-4 py-4">
                    <input type="hidden" name="reduce_motion" value="0" />
                    <input type="checkbox" name="reduce_motion" value="1" @checked(old('reduce_motion', $user->reduce_motion)) class="mt-1 h-4 w-4 rounded border-[color:var(--line-strong)] text-[color:var(--accent)]" />
                    <span>
                        <span class="block text-[13px] font-bold text-[color:var(--ink)]">Reducir animaciones</span>
                        <span class="mt-1 block text-[13px] leading-6 text-[color:var(--muted)]">Desactiva transiciones y efectos si prefieres una navegación más sobria.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-[22px] border border-[color:var(--line)] bg-white/80 px-4 py-4">
                    <input type="hidden" name="show_quick_notifications" value="0" />
                    <input type="checkbox" name="show_quick_notifications" value="1" @checked(old('show_quick_notifications', $user->show_quick_notifications)) class="mt-1 h-4 w-4 rounded border-[color:var(--line-strong)] text-[color:var(--accent)]" />
                    <span>
                        <span class="block text-[13px] font-bold text-[color:var(--ink)]">Mostrar panel rápido de notificaciones en perfil</span>
                        <span class="mt-1 block text-[13px] leading-6 text-[color:var(--muted)]">Oculta el lateral si quieres una página de perfil más limpia y centrada.</span>
                    </span>
                </label>

                <button type="submit" class="ui-button-primary inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Guardar ajustes</button>
            </form>
        </section>

        <section class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Seguridad</p>
            <h3 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Cambiar contrasena</h3>

            @if(session('password_status'))
                <div role="status" class="mt-5 rounded-2xl border border-[rgba(47,111,89,0.22)] bg-[rgba(47,111,89,0.08)] px-4 py-3 text-[13px] text-[color:var(--success)]">{{ session('password_status') }}</div>
            @endif

            @if($errors->passwordUpdate->any())
                <div role="alert" class="mt-5 rounded-2xl border border-[rgba(160,63,81,0.22)] bg-[rgba(160,63,81,0.08)] px-4 py-3 text-[13px] text-[color:var(--danger)]">{{ $errors->passwordUpdate->first() }}</div>
            @endif

            <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Contrasena actual</label>
                    <input id="current_password" name="current_password" type="password" required class="ui-field w-full rounded-2xl px-4 py-3 text-[14px]" />
                </div>
                <div>
                    <label for="password" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Nueva contrasena</label>
                    <input id="password" name="password" type="password" required class="ui-field w-full rounded-2xl px-4 py-3 text-[14px]" />
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Confirmar nueva contrasena</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="ui-field w-full rounded-2xl px-4 py-3 text-[14px]" />
                </div>
                <p class="text-[13px] leading-6 text-[color:var(--muted)]">La nueva contrasena debe tener al menos 8 caracteres y se valida contra la actual antes de guardarla.</p>
                <button type="submit" class="ui-button-primary inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Actualizar contrasena</button>
            </form>
        </section>
    </div>
</div>

<div class="grid gap-6 {{ $showQuickNotifications ? 'xl:grid-cols-[1.15fr_0.85fr]' : '' }}">
    <div class="space-y-6">
        <section class="app-surface-strong rounded-[28px] p-6 sm:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="flex items-start gap-5">
                    <div class="flex h-20 w-20 items-center justify-center rounded-[28px] bg-[color:var(--accent-soft)] text-2xl font-bold uppercase text-[color:var(--accent-strong)]">{{ $initials ?: 'US' }}</div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Perfil activo</p>
                        <h2 class="mt-2 text-2xl font-extrabold text-[color:var(--ink)]">{{ $user->name }}</h2>
                        <p class="mt-2 text-[14px] text-[color:var(--muted)]">{{ $user->email }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="rounded-full border border-[color:var(--line)] bg-[color:var(--accent-soft)] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid min-w-[220px] gap-3 sm:grid-cols-2 md:grid-cols-1 xl:grid-cols-2">
                    <article class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Cursos accesibles</p>
                        <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $courses->count() }}</p>
                    </article>
                    <article class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Notificaciones</p>
                        <p class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">{{ $recentNotifications->count() }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <article class="app-surface-strong rounded-[28px] p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Cursos</p>
                        <h3 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Resumen de acceso</h3>
                    </div>
                    <a href="{{ route('courses.index') }}" class="rounded-full border border-[color:var(--line)] bg-white px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]">Ver cursos</a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($courses->take(5) as $course)
                        @php($lastPr = $course->prs->first())
                        <a href="{{ route('courses.pr.view', $course->id) }}" class="block rounded-[24px] border border-[color:var(--line)] bg-white/80 px-5 py-4 transition hover:border-[color:var(--line-strong)] hover:bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">{{ $course->code }}</p>
                                    <h4 class="mt-1 text-[15px] font-bold text-[color:var(--ink)]">{{ $course->name }}</h4>
                                    <p class="mt-2 text-[13px] text-[color:var(--muted)]">{{ $lastPr ? 'Ultimo proyecto: Proyecto ' . $lastPr->number : 'Sin proyectos registrados' }}</p>
                                </div>
                                <span class="rounded-full bg-[color:var(--accent-soft)] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $course->prs->count() }} proyectos</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/60 px-5 py-8 text-center text-[14px] text-[color:var(--muted)]">No hay cursos asignados al usuario.</div>
                    @endforelse
                </div>
            </article>

            <article class="app-surface-strong rounded-[28px] p-6">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Actividad aproximada</p>
                <h3 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Ultimo proyecto o documento relacionado</h3>
                @if($latestPr)
                    <div class="mt-5 rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Proyecto reciente</p>
                        <h4 class="mt-3 text-2xl font-extrabold text-[color:var(--ink)]">Proyecto {{ $latestPr->number }}</h4>
                        <p class="mt-2 text-[14px] text-[color:var(--muted)]">Fase actual: {{ $latestPr->fase }}</p>
                        <p class="mt-1 text-[14px] text-[color:var(--muted)]">Curso asociado: {{ $latestPr->course?->name ?? 'Curso no disponible' }}</p>
                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ route('pr.documentos.index', $latestPr->id) }}" class="ui-button-primary inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Ir a documentos</a>
                            @if($latestPr->course)
                                <a href="{{ route('courses.pr.view', $latestPr->course->id) }}" class="ui-button-soft inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Abrir curso</a>
                            @endif
                        </div>
                    </div>
                @elseif($latestNotification)
                    <div class="mt-5 rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Ultimo aviso</p>
                        <h4 class="mt-3 text-[18px] font-bold text-[color:var(--ink)]">{{ $latestNotification->tema }}</h4>
                        <p class="mt-2 text-[14px] leading-7 text-[color:var(--muted)]">{{ $latestNotification->mensaje }}</p>
                    </div>
                @else
                    <div class="mt-5 rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/60 px-5 py-8 text-center text-[14px] text-[color:var(--muted)]">Todavia no hay actividad reciente para mostrar.</div>
                @endif
            </article>
        </section>
    </div>

    @if($showQuickNotifications)
    <aside class="app-surface-strong rounded-[28px] p-6 sm:p-7">
        <div class="flex items-center justify-between gap-4 border-b border-[color:var(--line)] pb-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Notificaciones</p>
                <h3 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Panel rapido</h3>
            </div>
            <a href="{{ route('notificaciones.index') }}" class="rounded-full border border-[color:var(--line)] bg-white px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]">Expandir</a>
        </div>

        <div class="mt-5 max-h-[680px] space-y-4 overflow-auto pr-1 user-scroll">
            @forelse($recentNotifications as $notification)
                <article class="rounded-[24px] border {{ $notification->fecha_lectura ? 'border-[color:var(--line)] bg-white/70' : 'border-[rgba(124,45,60,0.18)] bg-[rgba(124,45,60,0.06)]' }} p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[12px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">{{ $notification->tema }}</p>
                            <p class="mt-3 text-[14px] leading-7 text-[color:var(--muted)]">{{ $notification->mensaje }}</p>
                        </div>
                        @if(!$notification->fecha_lectura)
                            <span class="rounded-full bg-[color:var(--accent)] px-3 py-2 text-[10px] font-bold uppercase tracking-[0.16em] text-white">Nueva</span>
                        @endif
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <p class="text-[11px] uppercase tracking-[0.16em] text-[color:var(--muted)]">{{ optional($notification->fecha_envio ?? $notification->created_at)?->format('d/m/Y H:i') }}</p>
                        <a href="{{ route('notificaciones.open', $notification) }}" class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">Abrir</a>
                    </div>
                </article>
            @empty
                <div class="rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/60 px-5 py-8 text-center text-[14px] text-[color:var(--muted)]">No hay notificaciones para mostrar.</div>
            @endforelse
        </div>
    </aside>
    @endif
</div>
@endsection