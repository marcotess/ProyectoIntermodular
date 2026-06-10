<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Gestion de Cursos')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
       @if (file_exists(public_path('build/manifest.json')))
           @vite(['resources/css/app.css', 'resources/js/app.js'])
       @else
           <script src="{{ asset('app-fallback.js') }}" defer></script>
       @endif
    @php
        $dashboardUser = Auth::user();
        $themePreference = $dashboardUser?->theme_preference === 'dark' ? 'dark' : 'light';
        $compactTables = (bool) ($dashboardUser?->compact_tables ?? false);
        $reduceMotion = (bool) ($dashboardUser?->reduce_motion ?? false);
        $userInitials = collect(explode(' ', trim((string) $dashboardUser?->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
        $recentNavNotifications = $dashboardUser
            ? $dashboardUser->notificaciones()->orderByRaw('fecha_lectura is null desc')->orderByDesc('fecha_envio')->orderByDesc('created_at')->limit(5)->get()
            : collect();
        $unreadNotifications = $dashboardUser ? $dashboardUser->notificaciones()->whereNull('fecha_lectura')->count() : 0;
        $unreadChatMessages = $dashboardUser ? \App\Models\ChatMessage::query()->where('recipient_id', $dashboardUser->id)->whereNull('read_at')->count() : 0;
        $activeNav = trim($__env->yieldContent('activeNav'));
    @endphp
    <style>
        :root {
@if($themePreference === 'dark')
            --paper: #0f1115;
            --paper-strong: #141820;
            --panel: rgba(21, 26, 34, 0.86);
            --panel-strong: rgba(24, 30, 39, 0.94);
            --ink: #eef2f7;
            --muted: #a7b0bf;
            --line: rgba(180, 195, 214, 0.12);
            --line-strong: rgba(180, 195, 214, 0.22);
            --accent: #c7667c;
            --accent-soft: rgba(199, 102, 124, 0.14);
            --accent-strong: #f0b4c0;
            --success: #78c2a2;
            --danger: #f08ca0;
            --shadow: 0 28px 64px rgba(0, 0, 0, 0.30);
@else
            --paper: #f9f6f3;
            --paper-strong: #fffdfb;
            --panel: rgba(255, 252, 249, 0.86);
            --panel-strong: rgba(255, 255, 255, 0.92);
            --ink: #241b1f;
            --muted: #766670;
            --line: rgba(90, 40, 50, 0.10);
            --line-strong: rgba(90, 40, 50, 0.18);
            --accent: #7c2d3c;
            --accent-soft: #f2e4e8;
            --accent-strong: #5e2130;
            --success: #2f6f59;
            --danger: #a03f51;
            --shadow: 0 28px 64px rgba(77, 30, 41, 0.10);
@endif
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(124, 45, 60, 0.13), transparent 26%),
                radial-gradient(circle at top right, rgba(124, 45, 60, 0.08), transparent 20%),
                linear-gradient(180deg, var(--paper-strong) 0%, var(--paper) 100%);
        }

        body.theme-dark .bg-white,
        body.theme-dark .bg-white\/55,
        body.theme-dark .bg-white\/60,
        body.theme-dark .bg-white\/70,
        body.theme-dark .bg-white\/75,
        body.theme-dark .bg-white\/80 {
            background: rgba(20, 26, 34, 0.92) !important;
        }

        body.theme-dark .hover\:bg-white:hover {
            background: rgba(27, 35, 45, 0.96) !important;
        }

        body.theme-dark header {
            background: rgba(12, 16, 22, 0.88) !important;
        }

        body.theme-dark .shadow-sm {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.20) !important;
        }

        .brand-mark {
            font-family: 'Fraunces', serif;
            letter-spacing: 0.16em;
            transition: transform 180ms ease, opacity 180ms ease;
        }

        .brand-mark:hover {
            transform: scale(1.04);
        }

        .app-surface {
            background: var(--panel);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
        }

        .app-surface-strong {
            background: var(--panel-strong);
            border: 1px solid var(--line);
            box-shadow: 0 18px 40px rgba(77, 30, 41, 0.08);
        }

        .nav-link {
            color: var(--muted);
            transition: color 160ms ease, background 160ms ease, border-color 160ms ease;
        }

        .nav-link:hover {
            color: var(--accent-strong);
        }

        .nav-link-active {
            color: var(--accent-strong);
            background: rgba(124, 45, 60, 0.08);
            border-color: rgba(124, 45, 60, 0.15);
        }

        .page-table thead {
            background: rgba(124, 45, 60, 0.04);
        }

        .page-table tbody tr {
            transition: background 180ms ease;
        }

        .page-table tbody tr:hover {
            background: rgba(124, 45, 60, 0.04);
        }

        body.compact-tables .page-table th,
        body.compact-tables .page-table td {
            padding-top: 0.8rem !important;
            padding-bottom: 0.8rem !important;
        }

        .ui-field,
        .ui-select {
            border: 1px solid rgba(90, 40, 50, 0.12);
            background: rgba(255,255,255,0.92);
            color: var(--ink);
        }

        .ui-button-primary {
            background: var(--accent);
            color: white;
        }

        .ui-button-primary:hover {
            background: var(--accent-strong);
        }

        .ui-button-soft {
            background: rgba(124, 45, 60, 0.08);
            color: var(--accent-strong);
            border: 1px solid rgba(124, 45, 60, 0.12);
        }

        .user-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(124, 45, 60, 0.28) transparent;
        }

        .user-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .user-scroll::-webkit-scrollbar-thumb {
            background: rgba(124, 45, 60, 0.24);
            border-radius: 999px;
        }

        body.reduce-motion *,
        body.reduce-motion *::before,
        body.reduce-motion *::after {
            animation: none !important;
            transition: none !important;
            scroll-behavior: auto !important;
        }
    </style>
</head>
<body class="theme-{{ $themePreference }} {{ $compactTables ? 'compact-tables' : '' }} {{ $reduceMotion ? 'reduce-motion' : '' }}">
    <header class="sticky top-0 z-40 border-b border-[color:var(--line)] bg-[rgba(255,251,248,0.86)] backdrop-blur-xl">
        <div class="mx-auto flex max-w-[1480px] items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4 lg:gap-8">
                <a href="{{ route('profile') }}" class="brand-mark text-[12px] font-semibold uppercase text-[color:var(--accent-strong)] sm:text-[14px]">Gestion de Cursos</a>
                <nav aria-label="Navegacion principal" class="hidden items-center gap-2 md:flex">
                    <a href="{{ route('courses.index') }}" class="nav-link rounded-full border border-transparent px-4 py-2 text-[12px] font-semibold uppercase tracking-[0.16em] {{ $activeNav === 'courses' ? 'nav-link-active' : '' }}">Cursos</a>
                    <a href="{{ route('plantillas.index') }}" class="nav-link rounded-full border border-transparent px-4 py-2 text-[12px] font-semibold uppercase tracking-[0.16em] {{ $activeNav === 'plantillas' ? 'nav-link-active' : '' }}">Plantillas</a>
                    <a href="{{ route('tasks.index') }}" class="nav-link rounded-full border border-transparent px-4 py-2 text-[12px] font-semibold uppercase tracking-[0.16em] {{ $activeNav === 'tasks' ? 'nav-link-active' : '' }}">Tareas</a>
                    <a href="{{ route('chat.index') }}" class="nav-link relative rounded-full border border-transparent px-4 py-2 text-[12px] font-semibold uppercase tracking-[0.16em] {{ $activeNav === 'chat' ? 'nav-link-active' : '' }}">Chat
                        @if($unreadChatMessages > 0)
                            <span class="ml-2 inline-flex min-h-[20px] min-w-[20px] items-center justify-center rounded-full bg-[color:var(--accent)] px-1 text-[10px] font-bold text-white">{{ min($unreadChatMessages, 99) }}</span>
                        @endif
                    </a>
                </nav>
            </div>

            <div class="flex items-center gap-3 sm:gap-4">
                @if($activeNav !== 'profile')
                    <a href="{{ route('profile') }}" class="hidden rounded-full border border-[color:var(--line)] bg-white/75 px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-white md:inline-flex">Perfil</a>
                @endif

                <div class="relative">
                    <a href="{{ route('notificaciones.index') }}" aria-label="Abrir notificaciones{{ $unreadNotifications > 0 ? ' (' . min($unreadNotifications, 9) . ' sin leer)' : '' }}" class="relative inline-flex h-11 w-11 items-center justify-center rounded-full border border-[color:var(--line)] bg-white/80 text-[color:var(--accent-strong)] transition hover:bg-white" title="Notificaciones">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        @if($unreadNotifications > 0)
                            <span class="absolute -right-1 -top-1 inline-flex min-h-[20px] min-w-[20px] items-center justify-center rounded-full bg-[color:var(--accent)] px-1 text-[10px] font-bold text-white">{{ min($unreadNotifications, 9) }}</span>
                        @endif
                    </a>
                </div>

                <div class="relative">
                    <button type="button" id="userDropdownTrigger" data-user-dropdown-trigger aria-controls="userDropdown" aria-expanded="false" aria-haspopup="menu" class="group flex items-center gap-3 rounded-full border border-[color:var(--line)] bg-white/75 px-3 py-2 shadow-sm transition hover:bg-white">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[color:var(--accent-soft)] text-[13px] font-bold uppercase text-[color:var(--accent-strong)]">{{ $userInitials ?: 'US' }}</div>
                        <div class="hidden text-left md:block">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $dashboardUser?->name }}</div>
                            <div class="text-[11px] text-[color:var(--muted)]">{{ $dashboardUser?->email }}</div>
                        </div>
                    </button>

                    <div id="userDropdown" data-user-dropdown role="menu" aria-labelledby="userDropdownTrigger" aria-hidden="true" class="app-surface-strong absolute right-0 mt-3 hidden w-[320px] overflow-hidden rounded-[24px]">
                        <div class="border-b border-[color:var(--line)] px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[color:var(--accent-soft)] text-[18px] font-bold uppercase text-[color:var(--accent-strong)]">{{ $userInitials ?: 'US' }}</div>
                                <div>
                                    <p class="text-[13px] font-bold text-[color:var(--ink)]">{{ $dashboardUser?->name }}</p>
                                    <p class="text-[12px] text-[color:var(--muted)]">{{ $dashboardUser?->email }}</p>
                                    <p class="mt-1 text-[11px] uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">{{ $dashboardUser?->roles->pluck('name')->join(', ') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-[260px] space-y-3 overflow-auto px-5 py-4 user-scroll">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--muted)]">Notificaciones recientes</p>
                            @forelse($recentNavNotifications as $notification)
                                <a href="{{ route('notificaciones.open', $notification) }}" class="block rounded-2xl border border-[color:var(--line)] bg-white/75 px-4 py-3 transition hover:border-[color:var(--line-strong)] hover:bg-white">
                                    <p class="text-[12px] font-semibold text-[color:var(--ink)]">{{ $notification->tema }}</p>
                                    <p class="mt-1 text-[12px] leading-5 text-[color:var(--muted)] line-clamp-2">{{ $notification->mensaje }}</p>
                                </a>
                            @empty
                                <div class="rounded-2xl border border-dashed border-[color:var(--line)] bg-white/55 px-4 py-6 text-center text-[12px] text-[color:var(--muted)]">No hay notificaciones recientes.</div>
                            @endforelse
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-[color:var(--line)] px-5 py-4">
                            <a href="{{ route('notificaciones.index') }}" class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)]">Ver todas</a>
                            <button type="button" data-logout-button class="rounded-full border border-[color:var(--line)] bg-white px-4 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]">Cerrar sesion</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-[1480px] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        @hasSection('hero')
            @yield('hero')
        @endif

        <section class="app-surface rounded-[30px] p-4 sm:p-6 lg:p-8">
            <div class="mb-6 flex flex-col gap-4 border-b border-[color:var(--line)] pb-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.20em] text-[color:var(--accent-strong)]">Panel</p>
                    <h1 class="mt-2 text-2xl font-extrabold text-[color:var(--ink)] sm:text-3xl">@yield('pageTitle')</h1>
                    @hasSection('pageSubtitle')
                        <p class="mt-2 max-w-3xl text-[14px] leading-7 text-[color:var(--muted)]">@yield('pageSubtitle')</p>
                    @endif
                </div>
                @hasSection('pageActions')
                    <div class="flex flex-wrap items-center gap-3">
                        @yield('pageActions')
                    </div>
                @endif
            </div>

            @yield('content')
        </section>
    </main>
</body>
</html>