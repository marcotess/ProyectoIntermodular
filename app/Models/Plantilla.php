<?php

namespace App\Models;

use App\Actions\DocumentFilesystemAction;
use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    public $timestamps = false;
    protected $table = 'plantillas';

    protected $fillable = ['tipo_documento', 'prefijo', 'version'];

    public function getDisplayPrefijoAttribute(): string
    {
        $base = preg_replace('/\d+$/', '', (string) $this->prefijo) ?: (string) $this->prefijo;

        return $base . str_pad((string) $this->version, 2, '0', STR_PAD_LEFT);
    }

    public function getFileUrlAttribute(): ?string
    {
        // Devuelve la URL publica del archivo guardado en storage.
        $path = app(DocumentFilesystemAction::class)->resolvePlantillaPath($this);

        if (! $path) {
            return null;
        }

        return asset('storage/' . $path);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}