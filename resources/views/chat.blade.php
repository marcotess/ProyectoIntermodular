@extends('layouts.dashboard')

@section('title', 'Chat | Gestion de Cursos')
@section('activeNav', 'chat')
@section('pageTitle', 'Chat')
@section('pageSubtitle', 'Habla con docentes y revisores desde un solo panel. Las conversaciones iniciadas se mantienen visibles en esta sección.')

@section('pageActions')
    <a href="{{ route('chat.index', ['search' => $showSearch ? 0 : 1]) }}" class="ui-button-soft inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">
        {{ $showSearch ? 'Ocultar búsqueda' : 'Buscar' }}
    </a>
@endsection

@section('content')
<!-- vista principal del chat: a la izquierda se mueven conversaciones y busqueda, a la derecha vive el hilo abierto. -->
<div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
    <aside class="space-y-6">
        <section aria-labelledby="chat-conversations-title" class="app-surface-strong rounded-[28px] p-6">
            <div class="flex items-center justify-between gap-3 border-b border-[color:var(--line)] pb-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Conversaciones</p>
                    <h2 id="chat-conversations-title" class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Tus chats activos</h2>
                </div>
                <span aria-label="{{ $conversationContacts->count() }} conversaciones activas" class="rounded-full border border-[color:var(--line)] bg-white/70 px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-[color:var(--muted)]">{{ $conversationContacts->count() }}</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($conversationContacts as $contact)
                    @php
                        $isSelected = $selectedContact?->id === $contact->id;
                    @endphp
                    <a href="{{ route('chat.show', ['contact' => $contact->id]) }}" class="block rounded-[22px] border px-4 py-4 transition {{ $isSelected ? 'border-[color:var(--line-strong)] bg-[color:var(--accent-soft)]/70' : 'border-[color:var(--line)] bg-white/70 hover:border-[color:var(--line-strong)] hover:bg-white' }}">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[14px] font-bold text-[color:var(--ink)]">{{ $contact->name }}</p>
                                <p class="mt-1 text-[11px] uppercase tracking-[0.14em] text-[color:var(--muted)]">{{ $contact->roles->pluck('name')->join(' · ') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-[20px] text-[color:var(--accent-strong)]">chat</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-[22px] border border-dashed border-[color:var(--line)] bg-white/55 px-4 py-8 text-center text-[13px] leading-7 text-[color:var(--muted)]">
                        Aun no tienes conversaciones iniciadas. Usa el boton Buscar para elegir con quien hablar.
                    </div>
                @endforelse
            </div>
        </section>

        @if($showSearch)
            <!-- este panel solo aparece cuando el usuario pulsa buscar para empezar conversaciones nuevas. -->
            <section aria-labelledby="chat-search-title" class="app-surface-strong rounded-[28px] p-6">
                <div class="border-b border-[color:var(--line)] pb-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Buscar personas</p>
                    <h2 id="chat-search-title" class="mt-2 text-xl font-extrabold text-[color:var(--ink)]">Docentes y revisores disponibles</h2>
                </div>

                <div class="mt-5 space-y-3 max-h-[420px] overflow-auto pr-1 user-scroll">
                    @forelse($availableContacts as $contact)
                        <a href="{{ route('chat.show', ['contact' => $contact->id, 'search' => 1]) }}" class="flex items-center justify-between gap-3 rounded-[22px] border border-[color:var(--line)] bg-white/70 px-4 py-4 transition hover:border-[color:var(--line-strong)] hover:bg-white">
                            <div>
                                <p class="text-[14px] font-bold text-[color:var(--ink)]">{{ $contact->name }}</p>
                                <p class="mt-1 text-[11px] uppercase tracking-[0.14em] text-[color:var(--muted)]">{{ $contact->roles->pluck('name')->join(' · ') }}</p>
                            </div>
                            <span class="material-symbols-outlined text-[20px] text-[color:var(--accent-strong)]">person_search</span>
                        </a>
                    @empty
                        <div class="rounded-[22px] border border-dashed border-[color:var(--line)] bg-white/55 px-4 py-8 text-center text-[13px] text-[color:var(--muted)]">
                            No hay docentes ni revisores disponibles para iniciar chat.
                        </div>
                    @endforelse
                </div>
            </section>
        @endif
    </aside>

    <!-- aqui se muestra el hilo actual o, si no hay contacto, el estado vacio con la llamada a buscar. -->
    <section class="app-surface-strong rounded-[28px] p-6 sm:p-8">
        @if($selectedContact)
            <div class="flex flex-col gap-4 border-b border-[color:var(--line)] pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Conversación abierta</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-[color:var(--ink)]">{{ $selectedContact->name }}</h2>
                    <p class="mt-2 text-[13px] uppercase tracking-[0.14em] text-[color:var(--muted)]">{{ $selectedContact->roles->pluck('name')->join(' · ') }}</p>
                </div>
                <a href="{{ route('chat.index', ['search' => 1]) }}" class="ui-button-soft inline-flex rounded-full px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Cambiar conversación</a>
            </div>

            <div role="log" aria-live="polite" aria-label="Historial de mensajes" class="mt-6 space-y-4 max-h-[560px] overflow-auto pr-2 user-scroll">
                @forelse($messages as $message)
                    @php
                        $isOwnMessage = $message->sender_id === auth()->id();
                    @endphp
                    <article class="flex {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-[24px] border px-5 py-4 {{ $isOwnMessage ? 'border-[color:var(--line-strong)] bg-[color:var(--accent-soft)] text-[color:var(--accent-strong)]' : 'border-[color:var(--line)] bg-white/75 text-[color:var(--ink)]' }}">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] {{ $isOwnMessage ? 'text-[color:var(--accent-strong)]' : 'text-[color:var(--muted)]' }}">
                                {{ $isOwnMessage ? 'Tú' : $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="mt-3 whitespace-pre-wrap text-[14px] leading-7">{{ $message->message }}</p>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/55 px-5 py-12 text-center text-[14px] leading-7 text-[color:var(--muted)]">
                        Todavia no hay mensajes con esta persona. Escribe el primero para que la conversación quede guardada en tu lista.
                    </div>
                @endforelse
            </div>

            <form action="{{ route('chat.messages.store', ['contact' => $selectedContact->id]) }}" method="POST" class="mt-6 border-t border-[color:var(--line)] pt-6">
                @csrf
                <label for="message" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Nuevo mensaje</label>
                <textarea id="message" name="message" rows="5" aria-describedby="message-help" class="ui-field mt-3 w-full rounded-[24px] px-4 py-4 text-[14px] leading-7" placeholder="Escribe tu mensaje..." required>{{ old('message') }}</textarea>
                <p id="message-help" class="mt-2 text-[12px] text-[color:var(--muted)]">Escribe el contenido del mensaje y pulsa enviar para guardarlo en esta conversación.</p>
                @error('message')
                    <p role="alert" class="mt-2 text-[13px] text-[color:var(--danger)]">{{ $message }}</p>
                @enderror
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="ui-button-primary inline-flex rounded-full px-5 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Enviar mensaje</button>
                </div>
            </form>
        @else
            <div class="flex min-h-[520px] items-center justify-center rounded-[24px] border border-dashed border-[color:var(--line)] bg-white/55 px-6 text-center">
                <div class="max-w-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[color:var(--accent-strong)]">Sin conversación seleccionada</p>
                    <h2 class="mt-3 text-3xl font-extrabold text-[color:var(--ink)]">Elige un docente o revisor</h2>
                    <p class="mt-4 text-[15px] leading-8 text-[color:var(--muted)]">Pulsa Buscar para ver las personas disponibles. En cuanto envíes el primer mensaje, ese chat quedará guardado en esta misma sección para que puedas volver sin buscar de nuevo.</p>
                    <a href="{{ route('chat.index', ['search' => 1]) }}" class="ui-button-primary mt-6 inline-flex rounded-full px-5 py-3 text-[11px] font-semibold uppercase tracking-[0.16em]">Buscar personas</a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection