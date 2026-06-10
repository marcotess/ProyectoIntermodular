<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentVariant;
use Illuminate\Support\Facades\DB;



// esta clase monta variantes nuevas y deja el alta un poco mas ordenada.
class CreateVariantAction
{
    public function create(Document $document, int $userId): DocumentVariant
    {
        return DB::transaction(function () use ($document, $userId) {
            $initialStatus = DocumentStatus::firstOrCreate([
                'name' => '01_desarrollo',
            ]);

            $document->loadMissing(['plantilla', 'pr.course']);

            // Comprueba que no exista otra variante incompatible para este documento.
            app(EnsureVariantStatusAvailabilityAction::class)->statusRules($document->id, $initialStatus->name);

            $nextVersion = ((int) $document->variants()->max('version')) + 1;

            // cCrea la variante en estado desarrollo
            $variant = DocumentVariant::create([
                'document_id' => $document->id,
                'version' => $nextVersion,
                'status_id' => $initialStatus->id,
                'created_by' => $userId,
            ]);

            //    copia el archivo base de la plantilla al sitio de la variante
            app(DocumentFilesystemAction::class)->createVariantFile($variant);

            return $variant->fresh();
        });
    }
}