<?php

namespace App\Actions;

use App\Models\DocumentStatus;
use App\Models\DocumentVariant;
use RuntimeException;

class EnsureVariantStatusAvailabilityAction
{
    //funcion que crea las reglas de negocio sobre las variantes
    public function statusRules(int $documentId, string $statusName, ?int $ignoredVariantId = null): void
    {
        // .
        $conflictingStatuses = DocumentStatus::conflictingStatusesFor($statusName);

        // obsoleto no tiene limite asi que no hace falta validar nada
        if ($conflictingStatuses === []) {
            return;
        }

        //Busca otra variante del mismo documento que ya ocupe ese tipop de estado
        $query = DocumentVariant::query()
            ->where('document_id', $documentId)
            ->whereHas('status', function ($statusQuery) use ($conflictingStatuses) {
                $statusQuery->whereIn('name', $conflictingStatuses);
            });

        //editar una variante existente, se excluye a si misma de la comprobacion
        if ($ignoredVariantId !== null) {
            $query->whereKeyNot($ignoredVariantId);
        }

        //si no existe ninguna variante en repatida se permite el cambioo
        if (! $query->exists()) {
            return;
        }

        // si existe una variante en ese mismo estado , para la ejecucion y manda error
        throw new RuntimeException(DocumentStatus::exclusivityErrorMessage($statusName) ?? 'No se puede asignar este estado a la variante.');
    }
}