<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// notificacion simple, pero muy necesaria para que el usuario no vaya medio a ciegas.
class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'tema',
        'user_id',
        'mensaje',
        'link',
        'fecha_envio',
        'fecha_lectura',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'fecha_lectura' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
