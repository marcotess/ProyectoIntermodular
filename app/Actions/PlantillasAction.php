<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\Plantilla;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

// esta parte mueve las plantillas y deja fuera del controlador lo relacionado con ficheros y busquedas.
class PlantillasAction
{
    private const DOCUMENT_TYPE_PREFIXES = [
        'DEFINICION' => 'PDE',
        'COMPETENCIA' => 'TDC',
        'TIMING' => 'PTI',
        'PLAN_TRABAJO' => 'PGC',
        'ESTIMACION_DEDICACION' => 'PED',
        'GUION100' => 'PTG',
        'GUION600' => 'PTG',
        'INSTALACION' => 'PDI',
        'MANUAL' => 'PDO',
        'PRESENTACION' => 'PRE',
        'EJERCICIOS' => 'PPE',
        'PRACTICA' => 'PPR',
        'CUESTIONARIO' => 'PPR',
    ];

    public function documentTypes(): array
    {
        return array_keys(self::DOCUMENT_TYPE_PREFIXES);
    }

    public function listAll(): Collection
    {
        return Plantilla::query()
            ->orderBy('tipo_documento')
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->get();
    }

    public function listByDocumentType(string $tipoDocumento): Collection
    {
        return Plantilla::query()
            ->where('tipo_documento', $tipoDocumento)
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->get();
    }

    public function listGroupedByDocumentType(): array
    {
        return $this->listAll()
            ->groupBy('tipo_documento')
            ->map(fn (Collection $plantillas) => $plantillas->sortByDesc(fn ($plantilla) => sprintf('%09d-%09d', $plantilla->version, $plantilla->id))->values())
            ->all();
    }

    public function latestByDocumentType(string $tipoDocumento): ?Plantilla
    {
        return Plantilla::query()
            ->where('tipo_documento', $tipoDocumento)
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    public function latestByDocumentTypeOrFail(string $tipoDocumento): Plantilla
    {
        $plantilla = $this->latestByDocumentType($tipoDocumento);

        if (! $plantilla) {
            throw new RuntimeException('No existe una plantilla creada para el tipo de documento ' . $tipoDocumento . '.');
        }

        return $plantilla;
    }

    public function assignLatestToDocument(Document $document): Document
    {
        $plantilla = $this->latestByDocumentTypeOrFail($document->type);

        $document->plantilla_id = $plantilla->id;
        $document->save();

        return $document->fresh('plantilla');
    }

    public function updateDocumentPlantilla(int $documentId, int $plantillaId): Document
    {
        return DB::transaction(function () use ($documentId, $plantillaId) {
            $document = Document::query()
                ->with(['pr.course', 'variants.status'])
                ->findOrFail($documentId);
            $plantilla = Plantilla::query()->findOrFail($plantillaId);

            if ($plantilla->tipo_documento !== $document->type) {
                throw new RuntimeException('La plantilla seleccionada no corresponde al tipo del documento.');
            }

            app(DocumentFilesystemAction::class)->requirePlantillaPath($plantilla);

            $document->plantilla_id = $plantilla->id;
            $document->save();

            $document->setRelation('plantilla', $plantilla);

            app(DocumentFilesystemAction::class)->syncDocumentVariantsFromPlantilla($document);

            return $document->fresh('plantilla');
        });
    }

    public function create(string $tipoDocumento, UploadedFile $archivo): Plantilla
    {
        return DB::transaction(function () use ($tipoDocumento, $archivo) {
            //Calcula la siguiente version de la plantilla para ese tipo.
            $nextVersion = ((int) Plantilla::where('tipo_documento', $tipoDocumento)->max('version')) + 1;
            $prefijo = $this->buildPrefijo($tipoDocumento, $nextVersion);

            // Primero crea el registro y despues guarda el archivo fisico.
            $plantilla = Plantilla::create([
                'tipo_documento' => $tipoDocumento,
                'prefijo' => $prefijo,
                'version' => $nextVersion,
            ]);

            app(DocumentFilesystemAction::class)->storePlantillaFile($plantilla, $archivo);

            return $plantilla;
        });
    }

    private function buildPrefijo(string $tipoDocumento, int $version): string
    {
        $base = self::DOCUMENT_TYPE_PREFIXES[$tipoDocumento] ?? null;

        if ($base === null) {
            throw new RuntimeException('No existe un prefijo configurado para el tipo de documento ' . $tipoDocumento . '.');
        }

        return $base . str_pad((string) $version, 2, '0', STR_PAD_LEFT);
    }
}