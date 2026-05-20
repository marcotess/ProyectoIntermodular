@extends('layouts.dashboard')

@section('title', 'PR | Gestion de Cursos')
@section('activeNav', 'courses')
@section('pageTitle', 'PR del curso ' . $course->code)
@section('pageSubtitle', 'Seguimiento de PR asociados a ' . $course->name . '. Desde aqui se mantiene la fase, la fecha limite y la asignacion docente del curso.')

@section('pageActions')
    @if($canManagePrs)
        <button class="ui-button-primary inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]" onclick="crearPR({{ $course->id }})">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Crear PR
        </button>
    @endif
@endsection

@section('content')
@php($fasesPR = \App\Models\PR::PHASES)
<div class="app-surface-strong overflow-hidden rounded-[28px] border border-[color:var(--line)]">
    <div class="overflow-x-auto user-scroll">
        <table class="page-table min-w-full border-collapse text-left text-[13px]">
            <thead>
                <tr class="border-b border-[color:var(--line)]">
                    <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">PR</th>
                    <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Fase</th>
                    <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Fecha limite</th>
                    <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Docentes</th>
                    <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prs as $pr)
                    @php
                        $docentes = $pr->teachers()->pluck('users.name', 'users.id');
                        $allDocentes = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'docente'))->get();
                    @endphp
                    <tr class="border-b border-[color:var(--line)] last:border-b-0 align-top">
                        <td class="px-5 py-4">
                            <a href="{{ route('pr.documentos.index', ['pr' => $pr->id]) }}" class="text-[15px] font-bold text-[color:var(--accent-strong)] hover:underline">PR {{ $pr->number }}</a>
                        </td>
                        <td class="px-5 py-4 text-[color:var(--ink)] font-semibold">
                            @if($canEditPr)
                                <select id="fase-select-{{ $pr->id }}" class="ui-select rounded-2xl px-3 py-2 text-[13px]" onchange="cambiarFase({{ $pr->id }})">
                                    @foreach($fasesPR as $fase)
                                        <option value="{{ $fase }}" @selected($pr->fase === $fase)>{{ $fase }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{ $pr->fase }}
                            @endif
                        </td>
                        <td class="px-5 py-4 text-[color:var(--muted)]">
                            <div id="fecha-limite-view-{{ $pr->id }}" class="flex flex-wrap items-center gap-2">
                                <span>{{ $pr->fecha_limite ?: 'Sin fecha' }}</span>
                                @if($canEditPr)
                                    <button class="ui-button-soft rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="showEditFechaLimite({{ $pr->id }})">Editar</button>
                                @endif
                            </div>
                            @if($canEditPr)
                                <div id="fecha-limite-edit-{{ $pr->id }}" class="hidden flex flex-wrap items-center gap-2">
                                    <input type="date" id="fecha-limite-input-{{ $pr->id }}" value="{{ $pr->fecha_limite }}" class="ui-field rounded-2xl px-3 py-2 text-[13px]" />
                                    <button class="ui-button-primary rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="updateFechaLimite({{ $pr->id }})">Guardar</button>
                                    <button class="ui-button-soft rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="hideEditFechaLimite({{ $pr->id }})">Cancelar</button>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-[color:var(--muted)]">
                            <div id="docentes-list-{{ $pr->id }}">
                                <span>{{ $docentes->map(fn ($name) => explode(' ', $name)[0])->implode(', ') ?: 'Sin docentes' }}</span>
                                @if($canManagePrs)
                                    <button class="ui-button-soft ml-2 rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="showEditDocentes({{ $pr->id }})">Editar</button>
                                @endif
                            </div>
                            @if($canManagePrs)
                                <div id="docentes-edit-{{ $pr->id }}" class="hidden space-y-3">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($docentes as $id => $name)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-[color:var(--line)] bg-white px-3 py-2 text-[11px] font-semibold text-[color:var(--ink)]">
                                                {{ explode(' ', $name)[0] }}
                                                <button onclick="removeDocente({{ $pr->id }}, {{ $id }})" class="text-[color:var(--danger)]"><span class="material-symbols-outlined text-[16px]">delete</span></button>
                                            </span>
                                        @endforeach
                                        <button class="ui-button-primary rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="showAddDocente({{ $pr->id }})">Agregar</button>
                                    </div>
                                    <div id="add-docente-select-{{ $pr->id }}" class="hidden flex flex-wrap items-center gap-2">
                                        <select id="add-docente-{{ $pr->id }}" class="ui-select rounded-2xl px-3 py-2 text-[13px]">
                                            @foreach($allDocentes as $docente)
                                                @if(!$docentes->has($docente->id))
                                                    <option value="{{ $docente->id }}">{{ explode(' ', $docente->name)[0] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button class="ui-button-primary rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="addDocente({{ $pr->id }})">Guardar</button>
                                        <button class="ui-button-soft rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="hideAddDocente({{ $pr->id }})">Cancelar</button>
                                    </div>
                                    <button class="ui-button-soft rounded-full px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em]" onclick="hideEditDocentes({{ $pr->id }})">Cerrar</button>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-[color:var(--muted)]">{{ $pr->created_at ? $pr->created_at->format('d/m/Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-[14px] text-[color:var(--muted)]">No hay PR registrados para este curso.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection