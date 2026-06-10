<?php

namespace App\Http\Controllers;

use App\Actions\NotificacionAction;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

// aqui se mueve todo lo del chat: conversaciones, lectura, envio y el paso por notificaciones.
class ChatController extends Controller
{
    public function index(Request $request): View
    {
        return $this->buildChatView($request);
    }

    public function show(Request $request, User $contact): View
    {
        return $this->buildChatView($request, $contact);
    }

    public function store(Request $request, User $contact, NotificacionAction $notificacionAction): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canChatWith($contact), 404);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ChatMessage::query()->create([
            'sender_id' => $user->id,
            'recipient_id' => $contact->id,
            'message' => trim($data['message']),
        ]);

        $notificacionAction->notifyChatMessage($user, $contact);

        return redirect()->route('chat.show', ['contact' => $contact->id]);
    }

    private function buildChatView(Request $request, ?User $contact = null): View
    {
        $user = $request->user();
        $showSearch = $request->boolean('search');
        $conversationContacts = $this->conversationContacts($user);
        $hasExplicitContact = $contact !== null;

        if ($contact) {
            abort_unless($user->canChatWith($contact), 404);
        }

        $selectedContact = $contact;

        if (! $selectedContact && $conversationContacts->isNotEmpty()) {
            $selectedContact = $conversationContacts->first();
        }

        $messages = collect();

        if ($selectedContact) {
            if ($hasExplicitContact) {
                ChatMessage::query()
                    ->where('sender_id', $selectedContact->id)
                    ->where('recipient_id', $user->id)
                    ->whereNull('read_at')
                    ->update([
                        'read_at' => Carbon::now(),
                    ]);
            }

            $messages = ChatMessage::query()
                ->with(['sender.roles', 'recipient.roles'])
                ->betweenUsers($user, $selectedContact)
                ->orderBy('created_at')
                ->get();
        }

        $availableContacts = $showSearch
            ? User::query()
                ->with('roles')
                ->whereKeyNot($user->id)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['gestor', 'docente', 'revisor']);
                })
                ->orderBy('name')
                ->get()
            : collect();

        return view('chat', [
            'availableContacts' => $availableContacts,
            'conversationContacts' => $conversationContacts,
            'messages' => $messages,
            'selectedContact' => $selectedContact,
            'showSearch' => $showSearch,
        ]);
    }

    private function conversationContacts(User $user): Collection
    {
        return ChatMessage::query()
            ->with(['sender.roles', 'recipient.roles'])
            ->where(function ($query) use ($user) {
                $query
                    ->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->latest()
            ->get()
            ->map(function (ChatMessage $message) use ($user) {
                return $message->sender_id === $user->id ? $message->recipient : $message->sender;
            })
            ->filter(fn (?User $contact) => $contact?->canBeChatContact())
            ->unique('id')
            ->values();
    }
}