<!DOCTYPE html>
<html class="dark" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Documentos del PR</title>
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
                    Documentos del PR #{{ $pr->number }} ({{ $pr->course->code }} - {{ $pr->course->name }})
                </h1>
                @if($isGestor)
                    <button onclick="showCreateDocumentForm()" class="ml-4 px-3 py-2 bg-primary text-xs rounded text-white font-bold hover:bg-primary/80 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">add</span> Crear documento
                    </button>
                @endif
            </div>
            @if($isGestor)
                <div id="create-document-form" class="hidden mb-4 max-w-md border border-white/[0.08] rounded-lg bg-[#16181d] p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[12px] font-medium uppercase tracking-[0.15em] text-slate-300">Nuevo documento</h2>
                        <button onclick="hideCreateDocumentForm()" class="text-slate-400 hover:text-white" title="Cerrar">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    <label for="document-type" class="block mb-2 text-[11px] uppercase tracking-[0.15em] text-slate-400">Tipo de documento</label>
                    <select id="document-type" class="w-full bg-[#222] text-white rounded px-3 py-2 mb-3 border border-white/[0.08]">
                        <option value="" selected disabled>Selecciona un tipo</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                    <div id="document-tema-wrapper" class="hidden">
                        <label for="document-tema" class="block mb-2 text-[11px] uppercase tracking-[0.15em] text-slate-400">Tema</label>
                        <input id="document-tema" type="number" step="1" class="w-full bg-[#222] text-white rounded px-3 py-2 mb-3 border border-white/[0.08]" placeholder="Introduce un numero de tema" disabled />
                    </div>
                    <div class="flex gap-2">
                        <button onclick="createDocument({{ $pr->id }})" class="px-3 py-2 bg-primary text-xs rounded text-white font-bold hover:bg-primary/80 transition-colors">Crear</button>
                        <button onclick="hideCreateDocumentForm()" class="px-3 py-2 bg-slate-600 text-xs rounded text-white font-bold hover:bg-slate-500 transition-colors">Cancelar</button>
                    </div>
                </div>
            @endif
            <div class="flex-grow overflow-auto custom-scrollbar border border-white/[0.04] rounded-lg">
                <table class="w-full border-collapse text-left text-[12px]">
                    <thead class="sticky top-0 bg-[#0f1115] z-10">
                        <tr class="border-b border-white/[0.08]">
                            <th class="w-14 px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px] text-center">Ver</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">ID</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Tipo</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Plantilla</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Tema</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Nombre documento</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Título corto</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Nombre canónico</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Estado</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px]">Revisor</th>
                            <th class="px-4 py-5 font-medium uppercase tracking-[0.15em] text-slate-500 text-[10px] text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.03]">
                        @forelse($documents as $document)
                        @php
                            $variants = $variantsByDocument[$document->id] ?? collect();
                            $statusSummary = $statusSummaryByDocument[$document->id] ?? [];
                            $documentDisplayName = $documentNamesByDocument[$document->id] ?? '';
                            $supportsTema = in_array($document->type, $temaEligibleTypes, true);
                            $canDeleteDocument = $variants->isEmpty();
                            $plantillasDisponibles = $plantillasByType[$document->type] ?? collect();
                        @endphp
                        <tr id="document-{{ $document->id }}" onclick="toggleVariants({{ $document->id }})" class="cursor-pointer hover:bg-white/[0.02] transition-colors">
                            <td class="px-4 py-3 text-center">
                                <button onclick="event.stopPropagation(); toggleVariants({{ $document->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.02] text-slate-300 hover:bg-white/[0.06]" title="Ver variantes">
                                    <span id="variants-icon-{{ $document->id }}" class="material-symbols-outlined text-[18px] transition-transform duration-200">keyboard_arrow_down</span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-white font-bold">{{ $document->id }}</td>
                            <td class="px-4 py-3 text-white font-bold">{{ $document->type }}</td>
                            <td class="px-4 py-3 text-white font-bold" onclick="event.stopPropagation()">
                                @if($isGestor)
                                    <select id="plantilla-select-{{ $document->id }}" onclick="event.stopPropagation()" onchange="event.stopPropagation(); updateDocumentPlantilla({{ $document->id }})" class="bg-[#222] text-white rounded px-2 py-1 border border-white/[0.08] min-w-[150px]" {{ $plantillasDisponibles->isEmpty() ? 'disabled' : '' }}>
                                        @forelse($plantillasDisponibles as $plantilla)
                                            <option value="{{ $plantilla->id }}" @selected($document->plantilla_id === $plantilla->id)>
                                                {{ $plantilla->display_prefijo }}
                                            </option>
                                        @empty
                                            <option value="" selected>No hay plantillas para este tipo</option>
                                        @endforelse
                                    </select>
                                @else
                                    @if($document->plantilla)
                                        {{ $document->plantilla->display_prefijo }}
                                    @else
                                        <span class="text-slate-500">No hay plantilla asociada a este documento</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-white font-bold" onclick="event.stopPropagation()">
                                @if($supportsTema && $canEditTema)
                                    <div class="flex items-center gap-2">
                                        <input id="tema-input-{{ $document->id }}" type="number" step="1" value="{{ $document->tema ?? '' }}" class="w-24 bg-[#222] text-white rounded px-2 py-1 border border-white/[0.08]" placeholder="Tema" />
                                        <button onclick="event.stopPropagation(); updateDocumentTema({{ $document->id }})" class="px-2 py-1 bg-primary text-xs rounded text-white hover:bg-primary/80 transition-colors">Guardar</button>
                                    </div>
                                @elseif($supportsTema)
                                    {{ $document->tema ?? '' }}
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            <td class="px-4 py-3 text-white font-bold">{{ $documentDisplayName }}</td>
                            <td class="px-4 py-3 text-white font-bold">{{ $document->short_title }}</td>
                            <td class="px-4 py-3 text-white font-bold">{{ $document->canonical_name }}</td>
                            <td class="px-4 py-3 text-white font-bold align-top">
                                <div class="space-y-1 text-[11px] font-medium text-slate-200">
                                    @foreach($statusSummary as $summary)
                                        <div>
                                            N: {{ $summary['label'] }} @if($summary['label'] === 'Desarrollo')({{ $summary['detail'] }})@endif: {{ $summary['count'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-white font-bold">
                                @php
                                    $revisores = $document->reviewers;
                                    $availableReviewers = $availableReviewersByDocument[$document->id] ?? collect();
                                @endphp
                                <div id="revisores-list-{{ $document->id }}">
                                    @if($revisores->count())
                                        {{ $revisores->pluck('name')->join(', ') }}
                                    @else
                                        <span class="text-slate-500">Sin revisor</span>
                                    @endif
                                    @if($isGestor)
                                        <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="event.stopPropagation(); showEditRevisores({{ $document->id }})">Editar</button>
                                    @endif
                                </div>
                                @if($isGestor)
                                    <div id="revisores-edit-{{ $document->id }}" class="hidden">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            @foreach($revisores as $rev)
                                                <span class="bg-[#222] text-white rounded px-2 py-1 flex items-center gap-1">
                                                    {{ explode(' ', $rev->name)[0] }}
                                                    <button onclick="event.stopPropagation(); removeRevisor({{ $document->id }}, {{ $rev->id }})" class="text-red-400 ml-1"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                                                </span>
                                            @endforeach
                                            <button class="ml-2 px-2 py-1 bg-green-600 text-xs rounded" onclick="event.stopPropagation(); showAddRevisor({{ $document->id }})">+</button>
                                        </div>
                                        <div id="add-revisor-select-{{ $document->id }}" class="hidden mt-2">
                                            <select id="add-revisor-{{ $document->id }}" class="bg-[#222] text-white rounded px-2 py-1">
                                                @forelse($availableReviewers as $revisor)
                                                    <option value="{{ $revisor->id }}">{{ $revisor->name }}</option>
                                                @empty
                                                    <option disabled selected>No hay revisores disponibles</option>
                                                @endforelse
                                            </select>
                                            <button class="ml-2 px-2 py-1 bg-primary text-xs rounded" onclick="event.stopPropagation(); addRevisor({{ $document->id }})">Agregar</button>
                                            <button class="ml-2 px-2 py-1 bg-slate-500 text-xs rounded" onclick="event.stopPropagation(); hideAddRevisor({{ $document->id }})">Cancelar</button>
                                        </div>
                                        <button class="mt-2 px-2 py-1 bg-slate-500 text-xs rounded" onclick="event.stopPropagation(); hideEditRevisores({{ $document->id }})">Cerrar</button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-white font-bold text-center">
                                @if($isGestor)
                                    <button onclick="event.stopPropagation(); removeDocument({{ $document->id }})" class="px-2 py-1 text-xs rounded transition-colors {{ $canDeleteDocument ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-slate-700 text-slate-400 cursor-not-allowed' }}" title="{{ $canDeleteDocument ? 'Eliminar documento' : 'Borra antes todas las variantes' }}" {{ $canDeleteDocument ? '' : 'disabled' }}>
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr id="variants-row-{{ $document->id }}" class="hidden bg-[#12151b]">
                            <td colspan="11" class="px-6 py-5">
                                <div class="rounded-xl border border-white/[0.06] bg-black/10 p-4">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-300">Variantes de {{ $document->short_title }}</h3>
                                            <p class="mt-1 text-[11px] text-slate-500">Se muestra la variante actual y el historico completo del documento.</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="rounded-full border border-white/[0.08] bg-white/[0.03] px-3 py-1 text-[10px] uppercase tracking-[0.18em] text-slate-400">{{ $variants->count() }} versiones</span>
                                            @if($isGestor)
                                                <button onclick="event.stopPropagation(); createVariant({{ $document->id }})" class="inline-flex items-center gap-1 rounded bg-primary px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-white hover:bg-primary/80">
                                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                                    Nueva variante
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($variants->isNotEmpty())
                                        <div class="overflow-hidden rounded-lg border border-white/[0.06]">
                                            <table class="w-full border-collapse text-left text-[12px]">
                                                <thead class="bg-white/[0.03]">
                                                    <tr class="border-b border-white/[0.06]">
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-slate-500">Versión</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-slate-500">Estado</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-slate-500">Fecha objetivo</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-slate-500">Drive</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-slate-500 text-center">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-white/[0.04]">
                                                    @foreach($variants as $variant)
                                                        <tr>
                                                            <td class="px-4 py-3 text-white font-semibold">v{{ $variant->version }}</td>
                                                            <td class="px-4 py-3 text-slate-200">
                                                                <select onchange="event.stopPropagation(); updateVariantStatus({{ $variant->id }}, this.value)" class="bg-[#222] text-white rounded px-2 py-1 border border-white/[0.08]">
                                                                    @foreach($variantStatuses as $status)
                                                                        <option value="{{ $status->id }}" {{ $variant->status_id === $status->id ? 'selected' : '' }}>{{ $statusLabels[$status->name] ?? $status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="px-4 py-3 text-slate-400">{{ $variant->deadline_target ? \Illuminate\Support\Carbon::parse($variant->deadline_target)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                                                            <td class="px-4 py-3 text-slate-400">
                                                                @if($variant->drive_link_url)
                                                                    <a href="{{ $variant->drive_link_url }}" target="_blank" rel="noreferrer" onclick="event.stopPropagation()" class="text-primary hover:underline">Abrir</a>
                                                                @else
                                                                    Sin enlace
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                @if($canRemoveVariants)
                                                                    <button onclick="event.stopPropagation(); removeVariant({{ $variant->id }})" class="inline-flex items-center justify-center rounded bg-red-600 px-2 py-1 text-white hover:bg-red-700" title="Borrar variante">
                                                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-[12px] text-slate-500">Este documento todavia no tiene variantes registradas.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-4 py-2 text-slate-500 text-center">No hay documentos para este PR.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        window.documentTemaTypes = @json($temaEligibleTypes);
    </script>

</body>
</html>
