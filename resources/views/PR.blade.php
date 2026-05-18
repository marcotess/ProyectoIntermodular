<!DOCTYPE html>
<html class="dark" lang="es">

<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Unified PR Course View</title>

 <meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

<link rel="stylesheet" href="/styles.css">
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

<header class="fixed top-0 left-0 right-0 z-50 bg-[#0f1115]/80 backdrop-blur-md border-b border-white/[0.04] px-8 py-4">

<div class="max-w-[1600px] mx-auto flex items-center justify-between">

<nav class="flex items-center space-x-10">

<button class="flex items-center justify-center p-2 rounded-md hover:bg-white/[0.03] transition-colors" onclick="toggleSidebar()">
<span class="material-symbols-outlined text-slate-500 text-[20px]">menu</span>
</button>

<div class="flex items-center space-x-8 text-[11px] font-medium uppercase tracking-[0.15em]">

<a class="text-white active-nav-link" href="/home">Home</a>
<a class="text-slate-500 hover:text-slate-300 transition-colors" href="/tareas">Tareas</a>
<a class="text-slate-500 hover:text-slate-300 transition-colors" href="/cronograma">Cronograma</a>
<a class="text-slate-500 hover:text-slate-300 transition-colors" href="/plantillas">Plantillas</a>
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
    <!-- Dropdown Menu -->
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
<span class="text-[11px] font-medium uppercase tracking-wider">Cursos</span>
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
PR - {{ $course->code }} {{ $course->name }}
<span class="text-slate-600 ml-2 font-normal text-[10px] tracking-widest">({{ $prs->count() }} registros)</span>
</h1>

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

@php $fasesPR = \App\Models\PR::PHASES; @endphp

<thead class="sticky top-0 bg-[#0f1115] z-10">

<tr class="border-b border-white/[0.08]">
    <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">PR</th>
    <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Fase</th>
    <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Fecha límite</th>
    <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Docentes</th>
    <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Fecha</th>
</tr>
</thead>


<tbody class="divide-y divide-white/[0.03]">

@forelse($prs as $pr)
<tr>
    <td class="px-4 py-3 text-white font-bold">
        <a href="{{ route('pr.documentos.index', ['pr' => $pr->id]) }}" class="text-primary hover:underline">PR {{ $pr->number }}</a>
    </td>
    <td class="px-4 py-3 text-white font-bold">
        @if($canEditPr)
            <select
                id="fase-select-{{ $pr->id }}"
                class="bg-[#222] text-white rounded px-2 py-1 border border-white/[0.08]"
                onchange="cambiarFase({{ $pr->id }})"
            >
                @foreach($fasesPR as $fase)
                    <option value="{{ $fase }}" @selected($pr->fase === $fase)>{{ $fase }}</option>
                @endforeach
            </select>
        @else
            {{ $pr->fase }}
        @endif
    </td>
    <td class="px-4 py-3 text-white font-bold">
        <div id="fecha-limite-view-{{ $pr->id }}">
            {{ $pr->fecha_limite ?: '-' }}
            @if($canEditPr)
                <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="showEditFechaLimite({{ $pr->id }})">Editar</button>
            @endif
        </div>
        @if($canEditPr)
            <div id="fecha-limite-edit-{{ $pr->id }}" class="hidden">
                <input type="date" id="fecha-limite-input-{{ $pr->id }}" value="{{ $pr->fecha_limite }}" class="bg-[#222] text-white rounded px-2 py-1" />
                <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="updateFechaLimite({{ $pr->id }})">Guardar</button>
                <button class="ml-2 px-2 py-1 bg-slate-500 text-xs rounded" onclick="hideEditFechaLimite({{ $pr->id }})">Cancelar</button>
            </div>
        @endif
    </td>
    <td class="px-4 py-3 text-white font-bold">
        @php
            $docentes = $pr->teachers()->pluck('users.name', 'users.id');
            $allDocentes = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'docente'); })->get();
        @endphp
        <div id="docentes-list-{{ $pr->id }}">
            {{ $docentes->map(function($n){ return explode(' ', $n)[0]; })->implode(', ') ?: '-' }}
            @if($canManagePrs)
                <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="showEditDocentes({{ $pr->id }})">Editar</button>
            @endif
        </div>
        @if($canManagePrs)
            <div id="docentes-edit-{{ $pr->id }}" class="hidden">
                <div class="flex flex-wrap gap-2 items-center">
                    @foreach($docentes as $id => $name)
                        <span class="bg-[#222] text-white rounded px-2 py-1 flex items-center gap-1">
                            {{ explode(' ', $name)[0] }}
                            <button onclick="removeDocente({{ $pr->id }}, {{ $id }})" class="text-red-400 ml-1"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                        </span>
                    @endforeach
                    <button class="ml-2 px-2 py-1 bg-green-600 text-xs rounded" onclick="showAddDocente({{ $pr->id }})">+</button>
                </div>
                <div id="add-docente-select-{{ $pr->id }}" class="hidden mt-2">
                    <select id="add-docente-{{ $pr->id }}" class="bg-[#222] text-white rounded px-2 py-1">
                        @foreach($allDocentes as $docente)
                            @if(!$docentes->has($docente->id))
                                <option value="{{ $docente->id }}">{{ explode(' ', $docente->name)[0] }}</option>
                            @endif
                        @endforeach 
                    </select>
                    <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="addDocente({{ $pr->id }})">Agregar</button>
                    <button class="ml-2 px-2 py-1 bg-slate-500 text-xs rounded" onclick="hideAddDocente({{ $pr->id }})">Cancelar</button>
                </div>
                <button class="mt-2 px-2 py-1 bg-slate-500 text-xs rounded" onclick="hideEditDocentes({{ $pr->id }})">Cerrar</button>
            </div>
        @endif
    </td>
    <td class="px-4 py-3 text-white font-bold">{{ $pr->created_at ? $pr->created_at->format('Y-m-d') : '-' }}</td>
</tr>
@empty
<tr>
<td class="px-4 py-2 text-center text-slate-500">
No hay PRs para este curso.
</td>
</tr>
@endforelse

</tbody>
</table>

</div>


<div class="flex-none py-4 px-1 flex justify-between items-center text-[10px] text-slate-600 uppercase tracking-widest">

@if($canManagePrs)
<button class="hover:text-slate-300 flex items-center gap-2" onclick="crearPR({{ $course->id }})">
<span class="material-symbols-outlined text-[16px]">add</span>
CREAR PR
</button>
@else
<div></div>
@endif

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