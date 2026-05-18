<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DocumentStatus extends Model
{
    protected $fillable = ['name'];

    public const ACTIVE_STATUSES = [
        '01_desarrollo',
        '02_candidato',
    ];

    public const PRODUCTION_STATUS = '03_produccion';

    public const OBSOLETE_STATUS = '04_obsoleto';

    public const DEFAULT_STATUSES = [
        '01_desarrollo',
        '02_candidato',
        '03_produccion',
        '04_obsoleto',
    ];

    public static function ensureDefaults(): Collection
    {
        return collect(self::DEFAULT_STATUSES)->map(function ($statusName) {
            return self::firstOrCreate(['name' => $statusName]);
        });
    }

    public static function conflictingStatusesFor(string $statusName): array
    {
        return match ($statusName) {
            '01_desarrollo', '02_candidato' => self::ACTIVE_STATUSES,
            self::PRODUCTION_STATUS => [self::PRODUCTION_STATUS],
            default => [],
        };
    }

    public static function exclusivityErrorMessage(string $statusName): ?string
    {
        return match ($statusName) {
            '01_desarrollo', '02_candidato' => 'Solo puede haber una variante activa (Desarrollo o Candidato) por documento.',
            self::PRODUCTION_STATUS => 'Solo puede haber una variante en Produccion por documento.',
            default => null,
        };
    }
}