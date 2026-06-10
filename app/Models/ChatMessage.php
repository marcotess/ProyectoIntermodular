<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// aunque parezca simple, de aqui sale el chat, el contador de no leidos y parte de la experiencia del usuario.
class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function scopeBetweenUsers(Builder $query, User $firstUser, User $secondUser): Builder
    {
        return $query->where(function (Builder $nestedQuery) use ($firstUser, $secondUser) {
            $nestedQuery
                ->where('sender_id', $firstUser->id)
                ->where('recipient_id', $secondUser->id);
        })->orWhere(function (Builder $nestedQuery) use ($firstUser, $secondUser) {
            $nestedQuery
                ->where('sender_id', $secondUser->id)
                ->where('recipient_id', $firstUser->id);
        });
    }

    public function scopeUnreadForRecipient(Builder $query, User $user): Builder
    {
        return $query
            ->where('recipient_id', $user->id)
            ->whereNull('read_at');
    }
}