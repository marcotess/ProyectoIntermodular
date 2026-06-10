<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

// este modelo junta las relaciones importantes del documento y deja la logica pesada para las acciones.
class Document extends Model
{
    public const TEMA_ENABLED_TYPES = [
        'GUION100',
        'GUION600',
        'MANUAL',
        'PRESENTACION',
        'EJERCICIOS',
        'CUESTIONARIO',
        'PRACTICA',
    ];

    protected $fillable = ['pr_id', 'plantilla_id', 'tema', 'type', 'short_title', 'canonical_name'];

    protected $casts = [
        'tema' => 'integer',
    ];

    public function pr()
    {
        return $this->belongsTo(PR::class);
    }

    public function plantilla()
    {
        return $this->belongsTo(Plantilla::class);
    }

    public function variants()
    {
        return $this->hasMany(DocumentVariant::class);
    }

    public function latestVariant(): HasOne
    {
        return $this->hasOne(DocumentVariant::class)->latestOfMany('version');
    }

    public function reviewers()
    {
        return $this->belongsToMany(User::class, 'document_reviewers');
    }

    public static function temaEnabledTypes(): array
    {
        return self::TEMA_ENABLED_TYPES;
    }

    public static function supportsTemaType(string $type): bool
    {
        return in_array($type, self::TEMA_ENABLED_TYPES, true);
    }

    public function supportsTema(): bool
    {
        return self::supportsTemaType($this->type);
    }
}
