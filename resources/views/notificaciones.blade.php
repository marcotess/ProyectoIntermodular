<!DOCTYPE html>
<html class="dark" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Notificaciones</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/styles.css">
    @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/js/app.js'])
    @endif
    <style>
        body { background-color: #1a1a1a; }
        .sidebar-closed aside { display: none; }
        .sidebar-open aside { display: block; width: 200px; }
    </style>
</head>

<body class="sidebar-closed">
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#0f1115]/80 backdrop-blur-md border-b border-white/[0.04] px-8 py-4">
        <div class="max-w-[1600px] mx-auto flex items-center justify-between">
            <nav class="flex items-center space-x-10">
                <button class="flex items-center justify-center p-2 rounded-md hover:bg-white/[0.03] transition-colors" onclick="toggleSidebar()">
                    <span class="material-symbols-outlined text-slate-500 text-[20px]">menu</span>
                </button>
                <div class="flex items-center space-x-8 text-[11px] font-medium uppercase tracking-[0.15em]">
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/home">Home</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/plantillas">Plantillas</a>
                    <a class="text-white active-nav-link" href="{{ route('notificaciones.index') }}">Notificaciones</a>
                </div>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('notificaciones.index') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-slate-300 transition-colors hover:bg-white/[0.08] hover:text-white" title="Notificaciones">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </a>
                <div class="relative">
                    <button class="flex items-center space-x-4 px-2 py-1 group" onclick="toggleUserDropdown()">
                        <div class="w-7 h-7 rounded-full bg-white/[0.05] border border-white/[0.08] flex items-center justify-center text-[10px] font-medium text-slate-300">US</div>
                        <span class="text-[11px] font-medium uppercase tracking-widest text-slate-400 group-hover:text-slate-200 transition-colors">Usuario</span>
                        <span class="material-symbols-outlined text-slate-600 group-hover:text-slate-400 text-lg">expand_more</span>
                    </button>
                    <div class="absolute right-0 mt-2 w-64 bg-[#16181d] border border-white/[0.08] rounded-md shadow-lg z-50 hidden" id="userDropdown">
                        <div class="p-4">
                            <div class="flex flex-col items-center text-center mb-4">
                                <div class="w-12 h-12 rounded-full bg-white/[0.03] border border-white/[0.1] flex items-center justify-center text-lg font-medium text-slate-200 mb-2">US</div>
                                <h3 class="text-[14px] font-medium text-white">{{ Auth::user()->name }}</h3>
                                <p class="text-[10px] text-slate-500 uppercase tracking-widest font-mono">ID: {{ Auth::user()->id }}</p>
                            </div>
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center space-x-2">
                                    <span class="material-symbols-outlined text-slate-500 text-sm">mail</span>
                                    <p class="text-[12px] text-slate-300">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="material-symbols-outlined text-slate-500 text-sm">badge</span>
                                    <p class="text-[11px] text-slate-300 font-medium">{{ Auth::user()->roles->pluck('name')->join(', ') }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="logoutUser()" class="w-full py-2 bg-white/[0.03] hover:bg-white/[0.06] text-slate-300 text-[10px] font-bold uppercase tracking-widest rounded border border-white/[0.08] transition-all">Cerrar Sesión</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="pt-[73px] h-screen flex max-w-[1600px] mx-auto p-8 overflow-hidden w-full relative">
        <section class="flex-grow flex flex-col min-w-0 h-full">
            <div class="flex-none mb-6 flex justify-between items-center">
                <h1 class="text-[13px] font-medium text-slate-300 uppercase tracking-[0.2em]">Notificaciones</h1>
            </div>
            <div class="flex-grow overflow-auto custom-scrollbar border border-white/[0.04] rounded-lg bg-[#101319] p-4">
                @php($displayTimezone = config('app.display_timezone', 'Europe/Madrid'))
                @forelse($notificaciones as $notificacion)
                    <article class="mb-3 rounded-xl border {{ $notificacion->fecha_lectura ? 'border-white/[0.06] bg-white/[0.02]' : 'border-primary/40 bg-primary/10' }} p-4 transition-colors last:mb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="mb-2 flex items-center gap-3">
                                    <h2 class="text-[12px] font-semibold uppercase tracking-[0.16em] text-white">{{ $notificacion->tema }}</h2>
                                    @if(!$notificacion->fecha_lectura)
                                        <span class="rounded-full bg-primary px-2 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white">Nueva</span>
                                    @endif
                                </div>
                                <p class="text-[13px] leading-6 text-slate-300">{{ $notificacion->mensaje }}</p>
                                <p class="mt-3 text-[11px] uppercase tracking-[0.14em] text-slate-500">
                                    Enviada {{ optional($notificacion->fecha_envio)?->timezone($displayTimezone)->format('d/m/Y H:i') ?? $notificacion->created_at->timezone($displayTimezone)->format('d/m/Y H:i') }}
                                    @if($notificacion->fecha_lectura)
                                        · Leida {{ $notificacion->fecha_lectura->timezone($displayTimezone)->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                @if($notificacion->link)
                                    <a href="{{ route('notificaciones.open', $notificacion) }}" class="inline-flex items-center gap-2 rounded-lg border border-white/[0.08] bg-white/[0.04] px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-200 transition-colors hover:bg-white/[0.08] hover:text-white">
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                        Abrir documento
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex h-full items-center justify-center text-slate-500 text-[12px] uppercase tracking-[0.15em]">
                        No tienes notificaciones.
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</body>

</html>