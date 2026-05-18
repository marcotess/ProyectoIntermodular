<!DOCTYPE html>
<html class="dark" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Plantillas</title>
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
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/tareas">Tareas</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/cronograma">Cronograma</a>
                    <a class="text-white active-nav-link" href="/plantillas">Plantillas</a>
                    <a class="text-slate-500 hover:text-slate-300 transition-colors" href="/seguimiento">Seguimiento</a>
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
    </header>

    <main class="pt-[73px] h-screen flex max-w-[1600px] mx-auto p-8 overflow-hidden w-full relative">
        <aside class="flex-shrink-0 overflow-hidden h-full" id="sidebar">
            <div class="space-y-1 w-full">
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group" href="#">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Tareas</span>
                    <span class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group" href="#">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Cronograma</span>
                    <span class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md bg-white/[0.04] text-white transition-all group" href="#">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Plantillas</span>
                    <span class="material-symbols-outlined text-primary text-[16px]">chevron_right</span>
                </a>
                <a class="flex items-center justify-between p-3 rounded-md hover:bg-white/[0.03] text-white hover:text-slate-300 transition-all group" href="#">
                    <span class="text-[11px] font-medium uppercase tracking-wider">Seguimiento</span>
                    <span class="material-symbols-outlined text-slate-600 group-hover:text-primary text-[16px]">chevron_right</span>
                </a>
            </div>
        </aside>

        <section class="flex-grow flex flex-col min-w-0 h-full">
            <div class="flex-none mb-6 flex justify-between items-center">
                <h1 class="text-[13px] font-medium text-slate-300 uppercase tracking-[0.2em]">
                    Plantillas
                    <span class="text-slate-600 ml-2 font-normal text-[10px] tracking-widest">({{ $plantillas->count() }} registros)</span>
                </h1>
                @if($isGestor)
                    <button onclick="showCreatePlantillaForm()" class="ml-4 px-3 py-2 bg-primary text-xs rounded text-white font-bold hover:bg-primary/80 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">add</span> Crear plantilla
                    </button>
                @endif
            </div>

            @if($isGestor)
                <div id="create-plantilla-form" class="hidden mb-4 max-w-md border border-white/[0.08] rounded-lg bg-[#16181d] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[12px] font-medium uppercase tracking-[0.15em] text-slate-300">Nueva plantilla</h2>
                        <button onclick="hideCreatePlantillaForm()" class="text-slate-400 hover:text-white" title="Cerrar">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    <label for="plantilla-tipo-documento" class="block mb-2 text-[11px] uppercase tracking-[0.15em] text-slate-400">Tipo de documento</label>
                    <select id="plantilla-tipo-documento" class="w-full bg-[#222] text-white rounded px-3 py-2 mb-3 border border-white/[0.08]">
                        <option value="" selected disabled>Selecciona un tipo</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                    <label for="plantilla-archivo" class="block mb-2 text-[11px] uppercase tracking-[0.15em] text-slate-400">Archivo adjunto</label>
                    <input id="plantilla-archivo" type="file" accept=".doc,.docx,.pdf" class="w-full bg-[#222] text-white rounded px-3 py-2 mb-3 border border-white/[0.08] file:mr-3 file:rounded file:border-0 file:bg-primary file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                    <p class="mb-3 text-[11px] text-slate-400">El prefijo se asigna automaticamente segun el tipo de documento y la version correlativa.</p>
                    <div class="flex gap-2">
                        <button onclick="createPlantilla()" class="px-3 py-2 bg-primary text-xs rounded text-white font-bold hover:bg-primary/80 transition-colors">Crear</button>
                        <button onclick="hideCreatePlantillaForm()" class="px-3 py-2 bg-slate-600 text-xs rounded text-white font-bold hover:bg-slate-500 transition-colors">Cancelar</button>
                    </div>
                </div>
            @endif

            <div class="flex-grow overflow-auto custom-scrollbar border border-white/[0.04] rounded-lg">
                <table class="w-full border-collapse text-left text-[12px]">
                    <thead class="sticky top-0 bg-[#0f1115] z-10">
                        <tr class="border-b border-white/[0.08]">
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Tipo documento</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Prefijo</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Archivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.03]">
                        @forelse($plantillas as $plantilla)
                            <tr>
                                <td class="px-4 py-3 text-white font-bold">{{ $plantilla->tipo_documento }}</td>
                                <td class="px-4 py-3 text-white font-bold">{{ $plantilla->display_prefijo }}</td>
                                <td class="px-4 py-3 text-slate-300">
                                    @if($plantilla->file_url)
                                        <a href="{{ $plantilla->file_url }}" target="_blank" rel="noreferrer" class="text-primary hover:underline">Abrir</a>
                                    @else
                                        Sin archivo
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-slate-500 text-center">No hay plantillas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>

</html>