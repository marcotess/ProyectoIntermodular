<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\DocumentVariant;
use App\Models\Plantilla;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

// aqui llevo la parte fisica de archivos, por que luego buscar rutas sueltas por el proyecto es un dolor.
class DocumentFilesystemAction
{
    private const DISK = 'public';
    private const PLANTILLA_EXTENSIONS = ['docx', 'doc', 'pdf'];

    // cada estado guarda un directorio para orrganizar luego
    private const STATUS_DIRECTORIES = [
        '01_desarrollo' => 'Desarrollo',
        '02_candidato' => 'Candidato',
        '03_produccion' => 'Produccion',
        '04_obsoleto' => 'Obsoleto',
    ];
 
    //  guarda el archivo subido de la plantilla en  
    // 
    public function storePlantillaFile(Plantilla $plantilla, UploadedFile $file): void
    {    //       storage/public/plantillas

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'docx');
        $path = $this->buildPlantillaPath($plantilla, $extension);

        Storage::disk(self::DISK)->putFileAs('plantillas', $file, basename($path));
    }

    //    crea el archivo inicial de la variante copiando el archivo adjuntado e la plantilla 
    public function createVariantFile(DocumentVariant $variant): void
    {
        $this->syncVariantFileFromPlantilla($variant);
    }

    //Mueve el archivo de la variante cuando cambia de estado
    public function moveVariantFile(DocumentVariant $variant): void
    {
        $variant->loadMissing(['document.pr.course', 'status']);

        $sourcePath = $this->variantPathFromUrl($variant->drive_link_url);

        if (! $sourcePath) {
            return;
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($sourcePath)) {
            return;
        }

        $destinationPath = $this->buildVariantPath($variant, pathinfo($sourcePath, PATHINFO_EXTENSION));

        if ($sourcePath !== $destinationPath) {
            $disk->makeDirectory(dirname($destinationPath));
            $disk->move($sourcePath, $destinationPath);
        }

        $variant->forceFill(['drive_link_url' => $disk->url($destinationPath)])->saveQuietly();
    }

    // Borra el archivo  cuando se elimina la variante
    public function deleteVariantFile(DocumentVariant $variant): void
    {
        $path = $this->variantPathFromUrl($variant->drive_link_url);

        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public function resolveVariantPath(DocumentVariant $variant): ?string
    {
        return $this->variantPathFromUrl($variant->drive_link_url);
    }

    public function requireVariantPath(DocumentVariant $variant): string
    {
        $path = $this->resolveVariantPath($variant);

        if (! $path || ! Storage::disk(self::DISK)->exists($path)) {
            throw new RuntimeException('La variante seleccionada no tiene un archivo disponible.');
        }

        return $path;
    }

    // reemplaza el archivo de una variante por una copia fresca de la plantilla actual
    public function syncVariantFileFromPlantilla(DocumentVariant $variant): void
    {
        $variant->loadMissing(['document.plantilla', 'document.pr.course', 'status']);

        $plantilla = $variant->document?->plantilla;

        if (! $plantilla) {
            throw new RuntimeException('El documento no tiene una plantilla asignada.');
        }

        $sourcePath = $this->requirePlantillaPath($plantilla);
        $disk = Storage::disk(self::DISK);
        $destinationPath = $this->buildVariantPath($variant, pathinfo($sourcePath, PATHINFO_EXTENSION));
        $currentPath = $this->variantPathFromUrl($variant->drive_link_url);

        $disk->makeDirectory(dirname($destinationPath));

        if ($currentPath && $currentPath !== $destinationPath && $disk->exists($currentPath)) {
            $disk->delete($currentPath);
        }

        if ($disk->exists($destinationPath)) {
            $disk->delete($destinationPath);
        }

        $disk->copy($sourcePath, $destinationPath);

        $variant->forceFill(['drive_link_url' => $disk->url($destinationPath)])->saveQuietly();
    }

    // reaplica la plantilla actual del documento a todas sus variantes ya creadas.
    public function syncDocumentVariantsFromPlantilla(Document $document): void
    {
        $document->loadMissing(['plantilla', 'pr.course', 'variants.status']);

        foreach ($document->variants as $variant) {
            $variant->setRelation('document', $document);
            $this->syncVariantFileFromPlantilla($variant);
        }
    }

    // Construye la ruta fija de la plantilla usando su prefijo.
    private function buildPlantillaPath(Plantilla $plantilla, string $extension): string
    {
        return 'plantillas/' . $plantilla->display_prefijo . '.' . ltrim($extension, '.');
    }

    // Busca la plantilla guardada por su prefijo y extension.
    public function resolvePlantillaPath(Plantilla $plantilla): ?string
    {
        $disk = Storage::disk(self::DISK);

        foreach (self::PLANTILLA_EXTENSIONS as $extension) {
            $path = $this->buildPlantillaPath($plantilla, $extension);

            if ($disk->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public function requirePlantillaPath(Plantilla $plantilla): string
    {
        $path = $this->resolvePlantillaPath($plantilla);

        if (! $path) {
            throw new RuntimeException('La plantilla asociada no tiene archivo adjunto.');
        }

        return $path;
    }

    private function buildVariantPath(DocumentVariant $variant, string $extension): string
    {
        $pr = $variant->document?->pr;
        $course = $pr?->course;
        $statusName = $variant->status?->name ?? '01_desarrollo';
        $statusDirectory = self::STATUS_DIRECTORIES[$statusName] ?? null;

        if (! $pr || ! $course || ! $statusDirectory) {
            throw new RuntimeException('No se ha podido resolver la ruta de la variante.');
        }

        $courseDirectory = Str::of(($course->code ?? 'SIN_CURSO') . '_' . ($course->name ?? 'SIN_CURSO'))
            ->ascii()
            ->replaceMatches('/\s+/', '_')
            ->replaceMatches('/[^A-Za-z0-9_]/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->toString();

        return sprintf(
            'documents/%s/PR%s/%s/%s.%s',
            $courseDirectory,
            $pr->number,
            $statusDirectory,
            $variant->id,
            ltrim(strtolower($extension ?: 'docx'), '.')
        );
    }

    // onvierte el nombre del curso a un formato seguro para usarlo en carpetas
    private function variantPathFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || ! str_contains($path, '/storage/')) {
            return null;
        }

        return ltrim(Str::after($path, '/storage/'), '/');
    }
}