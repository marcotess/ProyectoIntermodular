<!DOCTYPE html>
<html class="dark" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Course Management Dashboard - Home</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&amp;family=JetBrains+Mono:wght@400&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="styles.css">
    @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/js/app.js'])
    @endif
    <style>
        body {
            background-color: #1a1a1a;
        }
        .sidebar-closed aside {
            display: none;
        }
        .sidebar-open aside {
            display: block;
            width: 200px;
        }
    </style>
</head>

<body class="sidebar-closed">
    <header
        class="fixed top-0 left-0 right-0 z-50 bg-[#0f1115]/80 backdrop-blur-md border-b border-white/[0.04] px-8 py-4">
        <div class="max-w-[1600px] mx-auto flex items-center justify-between">
            <nav class="flex items-center space-x-10">
                <button class="flex items-center justify-center p-2 rounded-md hover:bg-white/[0.03] transition-colors"
                    onclick="toggleSidebar()">
                    <span class="material-symbols-outlined text-slate-500 text-[20px]">menu</span>
                </button>
                <div class="flex items-center space-x-8 text-[11px] font-medium uppercase tracking-[0.15em]">
                    <a class="text-white active-nav-link" href="home.html">Home</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="tareas.html">Tareas</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors"
                        href="cronograma.html">Cronograma</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/plantillas">Plantillas</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="pr.html">PR</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors"
                        href="seguimiento.html">Seguimiento</a>
                    <span class="text-yellow-400 font-bold">Rol: {{ Auth::user()->roles->pluck('name')->join(', ') }}</span>
                </div>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('notificaciones.index') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-slate-300 transition-colors hover:bg-white/[0.08] hover:text-white" title="Notificaciones">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </a>
                <div class="relative">
                <button class="flex items-center space-x-4 px-2 py-1 group" onclick="toggleUserDropdown()">
                    <div
                        class="w-7 h-7 rounded-full bg-white/[0.05] border border-white/[0.08] flex items-center justify-center text-[10px] font-medium text-slate-300">
                        US</div>
                    <span
                        class="text-[11px] font-medium uppercase tracking-widest text-slate-400 group-hover:text-slate-200 transition-colors">Usuario</span>
                    <span
                        class="material-symbols-outlined text-slate-600 group-hover:text-slate-400 text-lg">expand_more</span>
                </button>
                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-64 bg-[#16181d] border border-white/[0.08] rounded-md shadow-lg z-50 hidden" id="userDropdown">
                    <div class="p-4">
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="w-12 h-12 rounded-full bg-white/[0.03] border border-white/[0.1] flex items-center justify-center text-lg font-medium text-slate-200 mb-2">
                                US
                            </div>
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
    </header>

    <main class="pt-[73px] h-screen flex max-w-[1600px] mx-auto p-8 overflow-hidden w-full relative">
        <aside class="flex-shrink-0 overflow-hidden h-full" id="sidebar">
            <div class="space-y-1 w-full">
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group"
                    href="tareas.html">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Tareas</span>
                    <span
                        class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group"
                    href="cronograma.html">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Cronograma</span>
                    <span
                        class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md bg-white/[0.04] text-white transition-all group"
                    href="home.html">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Cursos</span>
                    <span class="material-symbols-outlined text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group"
                    href="seguimiento.html">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Seguimiento</span>
                    <span
                        class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
            </div>
        </aside>

        <section class="flex-grow flex flex-col min-w-0 h-full">
            <div class="flex-none mb-6 flex justify-between items-center">
                <h1 class="text-[13px] font-medium text-slate-300 uppercase tracking-[0.2em]">Listado de Cursos <span
                        class="text-slate-600 ml-2 font-normal text-[10px] tracking-widest">({{ $courses->count() }} registros)</span></h1>
                <div class="flex items-center gap-4">
                    <button class="text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    </button>
                    <button class="text-slate-500 hover:text-slate-300 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                    </button>
                </div>
            </div>

            <div class="flex-grow overflow-auto custom-scrollbar border border-white/[0.04] rounded-lg">
                <table class="w-full border-collapse text-left text-[12px]">
                    <thead class="sticky top-0 bg-[#0f1115] z-10">
                        <tr class="border-b border-white/[0.08]">
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-white text-[10px]">Cursos</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-white text-[10px] text-center w-20">PR</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-white text-[10px]">Fecha límite</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-white text-[10px]">Docentes</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-white text-[10px]">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.03]">
                        @forelse($courses as $course)
                            <tr>
                                <td class="px-4 py-3 text-white font-bold">
                                    <a href="{{ route('courses.pr.view', $course->id) }}" class="text-primary hover:underline">{{ $course->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-center text-white font-bold">
                                    @php
                                        $lastPr = $course->prs()->orderBy('number', 'desc')->first();
                                    @endphp
                                    {{ $lastPr ? 'PR'.$lastPr->number : '-' }}
                                </td>
                                <td class="px-4 py-3 text-white font-bold">
                                    {{ $lastPr && $lastPr->fecha_limite ? $lastPr->fecha_limite : '-' }}
                                </td>
                                <td class="px-4 py-3 text-white font-bold">
                                    @php
                                        $docentes = $lastPr ? $lastPr->teachers()->pluck('name')->map(function($n) { return explode(' ', $n)[0]; }) : collect();
                                    @endphp
                                    {{ $docentes->implode(', ') ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-white font-bold">{{ $lastPr && $lastPr->created_at ? $lastPr->created_at->format('Y-m-d') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-slate-500">No tienes cursos asignados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="flex-none py-4 px-1 flex justify-between items-center text-[10px] text-slate-600 uppercase tracking-widest">
                <div>Mostrando {{ $courses->count() }} de {{ $courses->count() }} cursos</div>
                <div class="flex items-center gap-4">
                    <button class="hover:text-slate-300">Anterior</button>
                    <span class="text-primary">01</span>
                    <button class="hover:text-slate-300">Siguiente</button>
                </div>
            </div>
        </section>

    </main>

</body>

</html>
