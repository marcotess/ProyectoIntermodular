@extends('layouts.dashboard')

@section('title', 'Documentos | Gestion de Cursos')
@section('activeNav', 'courses')
@section('pageTitle', 'Documentos del PR #' . $pr->number)
@section('pageSubtitle', 'Gestion documental del curso ' . $pr->course->code . ' - ' . $pr->course->name . ', incluyendo variantes, plantillas, estado y revisores asociados.')

@section('pageActions')
    @if($isGestor)
        <button onclick="showCreateDocumentForm()" class="ui-button-primary inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Crear documento
        </button>
    @endif
@endsection

@section('content')
    @if($isGestor)
        <div id="create-document-form" class="app-surface-strong hidden mb-6 rounded-[28px] p-6 lg:max-w-[560px]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Nuevo documento</p>
                    <h2 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Alta documental</h2>
                </div>
                <button onclick="hideCreateDocumentForm()" class="rounded-full border border-[color:var(--line)] bg-white p-2 text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]" title="Cerrar">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="mt-5 space-y-4">
                <div>
                    <label for="document-type" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tipo de documento</label>
                    <select id="document-type" class="ui-select w-full rounded-2xl px-4 py-3 text-[14px]">
                        <option value="" selected disabled>Selecciona un tipo</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="document-tema-wrapper" class="hidden">
                    <label for="document-tema" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tema</label>
                    <input id="document-tema" type="number" step="1" class="ui-field w-full rounded-2xl px-4 py-3 text-[14px]" placeholder="Introduce un numero de tema" disabled />
                </div>
                <div class="flex flex-wrap gap-3">
                    <button onclick="createDocument({{ $pr->id }})" class="ui-button-primary rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Crear</button>
                    <button onclick="hideCreateDocumentForm()" class="ui-button-soft rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Cancelar</button>
                </div>
            </div>
        </div>
    @endif

    <div class="app-surface-strong overflow-hidden rounded-[28px] border border-[color:var(--line)]">
        <div class="overflow-x-auto user-scroll">
            <table class="page-table min-w-[1280px] border-collapse text-left text-[13px]">
                <thead>
                    <tr class="border-b border-[color:var(--line)]">
                        <th class="w-16 px-5 py-4 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Ver</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">ID</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tipo</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Plantilla</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tema</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Nombre documento</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Titulo corto</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Nombre canonico</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Estado</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Revisor</th>
                        <th class="px-5 py-4 text-center text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                        @forelse($documents as $document)
                        @php
                            $variants = $variantsByDocument[$document->id] ?? collect();
                            $statusSummary = $statusSummaryByDocument[$document->id] ?? [];
                            $documentDisplayName = $documentNamesByDocument[$document->id] ?? '';
                            $supportsTema = in_array($document->type, $temaEligibleTypes, true);
                            $canDeleteDocument = $variants->isEmpty();
                            $plantillasDisponibles = $plantillasByType[$document->type] ?? collect();
                        @endphp
                        <tr id="document-{{ $document->id }}" onclick="toggleVariants({{ $document->id }})" class="cursor-pointer border-b border-[color:var(--line)] last:border-b-0 transition-colors">
                            <td class="px-5 py-4 text-center">
                                <button onclick="event.stopPropagation(); toggleVariants({{ $document->id }})" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-[color:var(--line)] bg-white text-[color:var(--accent-strong)] hover:bg-[color:var(--accent-soft)]" title="Ver variantes">
                                    <span id="variants-icon-{{ $document->id }}" class="material-symbols-outlined text-[18px] transition-transform duration-200">keyboard_arrow_down</span>
                                </button>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">{{ $document->id }}</td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">{{ $document->type }}</td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold" onclick="event.stopPropagation()">
                                @if($isGestor)
                                    <select id="plantilla-select-{{ $document->id }}" onclick="event.stopPropagation()" onchange="event.stopPropagation(); updateDocumentPlantilla({{ $document->id }})" class="ui-select min-w-[170px] rounded-2xl px-3 py-2 text-[13px]" {{ $plantillasDisponibles->isEmpty() ? 'disabled' : '' }}>
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
                                        <span class="text-[color:var(--muted)]">No hay plantilla asociada a este documento</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold" onclick="event.stopPropagation()">
                                @if($supportsTema && $canEditTema)
                                    <div class="flex items-center gap-2">
                                        <input id="tema-input-{{ $document->id }}" type="number" step="1" value="{{ $document->tema ?? '' }}" class="ui-field w-24 rounded-2xl px-3 py-2 text-[13px]" placeholder="Tema" />
                                        <button onclick="event.stopPropagation(); updateDocumentTema({{ $document->id }})" class="ui-button-primary rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]">Guardar</button>
                                    </div>
                                @elseif($supportsTema)
                                    {{ $document->tema ?? '' }}
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">{{ $documentDisplayName }}</td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">{{ $document->short_title }}</td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">{{ $document->canonical_name }}</td>
                            <td class="px-5 py-4 align-top">
                                <div class="space-y-1 text-[11px] font-medium text-[color:var(--muted)]">
                                    @foreach($statusSummary as $summary)
                                        <div>
                                            N: {{ $summary['label'] }} @if($summary['label'] === 'Desarrollo')({{ $summary['detail'] }})@endif: {{ $summary['count'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--ink)] font-bold">
                                @php
                                    $revisores = $document->reviewers;
                                    $availableReviewers = $availableReviewersByDocument[$document->id] ?? collect();
                                @endphp
                                <div id="revisores-list-{{ $document->id }}">
                                    @if($revisores->count())
                                        {{ $revisores->pluck('name')->join(', ') }}
                                    @else
                                        <span class="text-[color:var(--muted)]">Sin revisor</span>
                                    @endif
                                    @if($isGestor)
                                        <button class="ui-button-soft ml-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="event.stopPropagation(); showEditRevisores({{ $document->id }})">Editar</button>
                                    @endif
                                </div>
                                @if($isGestor)
                                    <div id="revisores-edit-{{ $document->id }}" class="hidden">
                                        <div class="flex flex-wrap gap-2 items-center">
                                            @foreach($revisores as $rev)
                                                <span class="inline-flex items-center gap-2 rounded-full border border-[color:var(--line)] bg-white px-3 py-2 text-[11px] font-semibold text-[color:var(--ink)]">
                                                    {{ explode(' ', $rev->name)[0] }}
                                                    <button onclick="event.stopPropagation(); removeRevisor({{ $document->id }}, {{ $rev->id }})" class="text-[color:var(--danger)]"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                                                </span>
                                            @endforeach
                                            <button class="ui-button-primary ml-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="event.stopPropagation(); showAddRevisor({{ $document->id }})">Agregar</button>
                                        </div>
                                        <div id="add-revisor-select-{{ $document->id }}" class="hidden mt-2">
                                            <select id="add-revisor-{{ $document->id }}" class="ui-select rounded-2xl px-3 py-2 text-[13px]">
                                                @forelse($availableReviewers as $revisor)
                                                    <option value="{{ $revisor->id }}">{{ $revisor->name }}</option>
                                                @empty
                                                    <option disabled selected>No hay revisores disponibles</option>
                                                @endforelse
                                            </select>
                                            <button class="ui-button-primary ml-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="event.stopPropagation(); addRevisor({{ $document->id }})">Guardar</button>
                                            <button class="ui-button-soft ml-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="event.stopPropagation(); hideAddRevisor({{ $document->id }})">Cancelar</button>
                                        </div>
                                        <button class="ui-button-soft mt-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="event.stopPropagation(); hideEditRevisores({{ $document->id }})">Cerrar</button>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($isGestor)
                                    <button onclick="event.stopPropagation(); removeDocument({{ $document->id }})" class="rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em] transition-colors {{ $canDeleteDocument ? 'bg-[color:var(--danger)] text-white hover:opacity-90' : 'cursor-not-allowed bg-gray-200 text-gray-400' }}" title="{{ $canDeleteDocument ? 'Eliminar documento' : 'Borra antes todas las variantes' }}" {{ $canDeleteDocument ? '' : 'disabled' }}>
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                @else
                                    <span class="text-[color:var(--muted)]">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr id="variants-row-{{ $document->id }}" class="hidden bg-[rgba(124,45,60,0.03)]">
                            <td colspan="11" class="px-6 py-5">
                                <div class="rounded-[24px] border border-[color:var(--line)] bg-white/80 p-5">
                                    <div class="mb-4 flex items-center justify-between">
                                        <div>
                                            <h3 class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Variantes de {{ $document->short_title }}</h3>
                                            <p class="mt-1 text-[11px] text-[color:var(--muted)]">Se muestra la variante actual y el historico completo del documento.</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="rounded-full border border-[color:var(--line)] bg-white px-3 py-2 text-[10px] uppercase tracking-[0.18em] text-[color:var(--muted)]">{{ $variants->count() }} versiones</span>
                                            @if($isGestor)
                                                <button onclick="event.stopPropagation(); createVariant({{ $document->id }})" class="ui-button-primary inline-flex items-center gap-1 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.14em]">
                                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                                    Nueva variante
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    @if($variants->isNotEmpty())
                                        <div class="overflow-hidden rounded-[22px] border border-[color:var(--line)]">
                                            <table class="w-full border-collapse text-left text-[12px]">
                                                <thead class="bg-[rgba(124,45,60,0.04)]">
                                                    <tr class="border-b border-[color:var(--line)]">
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-[color:var(--muted)]">Version</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-[color:var(--muted)]">Estado</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-[color:var(--muted)]">Fecha objetivo</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-[color:var(--muted)]">Drive</th>
                                                        <th class="px-4 py-3 text-[10px] uppercase tracking-[0.15em] text-[color:var(--muted)] text-center">Accion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($variants as $variant)
                                                        <tr class="border-b border-[color:var(--line)] last:border-b-0">
                                                            <td class="px-4 py-3 text-[color:var(--ink)] font-semibold">v{{ $variant->version }}</td>
                                                            <td class="px-4 py-3 text-[color:var(--muted)]">
                                                                <select onchange="event.stopPropagation(); updateVariantStatus({{ $variant->id }}, this.value)" class="ui-select rounded-2xl px-3 py-2 text-[13px]">
                                                                    @foreach($variantStatuses as $status)
                                                                        <option value="{{ $status->id }}" {{ $variant->status_id === $status->id ? 'selected' : '' }}>{{ $statusLabels[$status->name] ?? $status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="px-4 py-3 text-[color:var(--muted)]">{{ $variant->deadline_target ? \Illuminate\Support\Carbon::parse($variant->deadline_target)->format('d/m/Y H:i') : 'Sin fecha' }}</td>
                                                            <td class="px-4 py-3 text-[color:var(--muted)]">
                                                                @if($variant->drive_link_url)
                                                                    <a href="{{ $variant->drive_link_url }}" target="_blank" rel="noreferrer" onclick="event.stopPropagation()" class="font-semibold text-[color:var(--accent-strong)] hover:underline">Abrir</a>
                                                                @else
                                                                    Sin enlace
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                @if($canRemoveVariants)
                                                                    <button onclick="event.stopPropagation(); removeVariant({{ $variant->id }})" class="inline-flex items-center justify-center rounded-full bg-[color:var(--danger)] px-3 py-2 text-white hover:opacity-90" title="Borrar variante">
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
                                        <p class="text-[12px] text-[color:var(--muted)]">Este documento todavia no tiene variantes registradas.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-5 py-10 text-center text-[14px] text-[color:var(--muted)]">No hay documentos para este PR.</td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.documentTemaTypes = @json($temaEligibleTypes);
    </script>
@endsection
