@extends('layouts.dashboard')

@section('title', 'Plantillas | Gestion de Cursos')
@section('activeNav', 'plantillas')
@section('pageTitle', 'Plantillas documentales')
@section('pageSubtitle', 'Gestiona las plantillas disponibles para los distintos tipos de documento y mantén una base coherente para la generacion de materiales.')

@section('pageActions')
    @if($isGestor)
        <button type="button" onclick="document.getElementById('create-plantilla-form')?.classList.remove('hidden')" class="ui-button-primary inline-flex items-center gap-2 rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Crear plantilla
        </button>
    @endif
@endsection

@section('content')
@php
    $showCreatePlantillaForm = $errors->any() || session('status');
@endphp
<div class="grid gap-6">
    @if($isGestor)
        <div id="create-plantilla-form" class="{{ $showCreatePlantillaForm ? '' : 'hidden' }} app-surface-strong rounded-[28px] p-6 lg:max-w-[560px]">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Nueva plantilla</p>
                    <h2 class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Alta de plantilla</h2>
                </div>
                <button type="button" onclick="document.getElementById('create-plantilla-form')?.classList.add('hidden')" class="rounded-full border border-[color:var(--line)] bg-white p-2 text-[color:var(--accent-strong)] transition hover:border-[color:var(--line-strong)] hover:bg-[color:var(--accent-soft)]" title="Cerrar">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            @if(session('status'))
                <div class="mt-5 rounded-2xl border border-[rgba(47,111,89,0.22)] bg-[rgba(47,111,89,0.08)] px-4 py-3 text-[13px] text-[color:var(--success)]">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="mt-5 rounded-2xl border border-[rgba(160,63,81,0.22)] bg-[rgba(160,63,81,0.08)] px-4 py-3 text-[13px] text-[color:var(--danger)]">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('plantillas.create') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="plantilla-tipo-documento" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tipo de documento</label>
                    <select id="plantilla-tipo-documento" name="tipo_documento" class="ui-select w-full rounded-2xl px-4 py-3 text-[14px]" required>
                        <option value="" selected disabled>Selecciona un tipo</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}" @selected(old('tipo_documento') === $type)>{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="plantilla-archivo" class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Archivo adjunto</label>
                    <input id="plantilla-archivo" name="archivo" type="file" accept=".doc,.docx,.pdf" required class="ui-field w-full rounded-2xl px-4 py-3 text-[14px] file:mr-3 file:rounded-full file:border-0 file:bg-[color:var(--accent)] file:px-4 file:py-2 file:text-[11px] file:font-semibold file:uppercase file:tracking-[0.16em] file:text-white" />
                </div>

                <p class="text-[13px] leading-7 text-[color:var(--muted)]">El prefijo se calcula automaticamente a partir del tipo de documento y la version correlativa asociada a la plantilla.</p>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="ui-button-primary rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Crear</button>
                    <button type="button" onclick="document.getElementById('create-plantilla-form')?.classList.add('hidden')" class="ui-button-soft rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div id="plantillas-list" class="app-surface-strong overflow-hidden rounded-[28px] border border-[color:var(--line)]">
        <div class="overflow-x-auto user-scroll">
            <table class="page-table min-w-full border-collapse text-left text-[13px]">
                <thead>
                    <tr class="border-b border-[color:var(--line)]">
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Tipo documento</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Prefijo</th>
                        <th class="px-5 py-4 text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--muted)]">Archivo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plantillas as $plantilla)
                        <tr class="border-b border-[color:var(--line)] last:border-b-0">
                            <td class="px-5 py-4 text-[color:var(--ink)] font-semibold">{{ $plantilla->tipo_documento }}</td>
                            <td class="px-5 py-4 text-[color:var(--accent-strong)] font-semibold">{{ $plantilla->display_prefijo }}</td>
                            <td class="px-5 py-4 text-[color:var(--muted)]">
                                @if($plantilla->file_url)
                                    <a href="{{ $plantilla->file_url }}" target="_blank" rel="noreferrer" class="font-semibold text-[color:var(--accent-strong)] hover:underline">Abrir archivo</a>
                                @else
                                    Sin archivo
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-[14px] text-[color:var(--muted)]">No hay plantillas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection