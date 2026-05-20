<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentVariant;
use App\Models\PR;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateDocumentAction
{
	/**
	 * Tipos documentales admitidos por el flujo de gestion del proyecto.
	 */
	private const DOCUMENT_TYPES = [
		'DEFINICION',
		'COMPETENCIA',
		'TIMING',
		'PLAN_TRABAJO',
		'ESTIMACION_DEDICACION',
		'GUION100',
		'GUION600',
		'INSTALACION',
		'MANUAL',
		'PRESENTACION',
		'EJERCICIOS',
		'PRACTICA',
		'CUESTIONARIO',
	];

	private const STATUS_LABELS = [
		'01_desarrollo' => 'Desarrollo',
		'02_candidato' => 'Candidato',
		'03_produccion' => 'Produccion',
		'04_obsoleto' => 'Obsoleto',
	];

	private const STATUS_ABBREVIATIONS = [
		'01_desarrollo' => 'D',
		'02_candidato' => 'C',
		'03_produccion' => 'P',
		'04_obsoleto' => 'O',
	];

	private const STATUS_SUMMARY_GROUPS = [
		'desarrollo' => [
			'label' => 'Desarrollo',
			'statuses' => ['01_desarrollo', '02_candidato'],
			'detail' => 'desarrollo, candidato',
		],
		'produccion' => [
			'label' => 'Produccion',
			'statuses' => ['03_produccion'],
			'detail' => 'produccion',
		],
		'obsoleto' => [
			'label' => 'Obsoleto',
			'statuses' => ['04_obsoleto'],
			'detail' => 'obsoleto',
		],
	];

	/**
	 * Devuelve los tipos de documento que pueden crearse en un PR.
	 *
	 * @return array<int, string>
	 */
	public function documentTypes(): array
	{
		return self::DOCUMENT_TYPES;
	}

	/**
	 * Devuelve las etiquetas legibles asociadas a cada estado interno.
	 *
	 * @return array<string, string>
	 */
	public function statusLabels(): array
	{
		return self::STATUS_LABELS;
	}

	/**
	 * Devuelve los tipos de documento que admiten tema.
	 *
	 * @return array<int, string>
	 */
	public function temaEligibleTypes(): array
	{
		return Document::temaEnabledTypes();
	}

	/**
	 * Indica si un tipo de documento permite asignar tema.
	 *
	 * @param string $type
	 * @return bool
	 */
	public function typeSupportsTema(string $type): bool
	{
		return Document::supportsTemaType($type);
	}

	/**
	 * Devuelve el estado inicial que se asigna a la primera variante del documento.
	 *
	 * @return string
	 */
	public function initialStatus(): string
	{
		return '01_desarrollo';
	}

	/**
	 * Lista las variantes de un documento ordenadas de la mas reciente a la mas antigua.
	 *
	 * @param Document $document
	 * @return Collection<int, DocumentVariant>
	 */
	public function listVariants(Document $document): Collection
	{
		return $document->variants()
			->with('status')
			->orderByDesc('version')
			->get();
	}

	/**
	 * Agrupa las variantes del documento en bloques de estado para mostrarlos resumidos.
	 *
	 * @param Document $document
	 * @param Collection<int, DocumentVariant>|null $variants
	 * @return array<int, array{label: string, detail: string, count: int}>
	 */
	public function summarizeVariantStatuses(Document $document, ?Collection $variants = null): array
	{
		$variants ??= $this->listVariants($document);
		$statusCounts = $variants
			->pluck('status.name')
			->filter()
			->countBy();

		return collect(self::STATUS_SUMMARY_GROUPS)
			->map(function (array $group) use ($statusCounts) {
				$count = collect($group['statuses'])
					->sum(fn (string $statusName) => (int) ($statusCounts[$statusName] ?? 0));

				return [
					'label' => $group['label'],
					'detail' => $group['detail'],
					'count' => $count,
				];
			})
			->values()
			->all();
	}

	/**
	 * Construye el nombre visible del documento a partir de plantilla, tipo, curso, tema, PR, version y estado.
	 *
	 * @param Document $document
	 * @param PR $pr
	 * @return string
	 */
	public function buildDisplayName(Document $document, PR $pr): string
	{
		$segments = [
			$this->buildPlantillaSegment($document),
			$document->type,
			$this->buildCourseSegment($pr),
		];

		$temaSegment = $this->buildTemaSegment($document);

		if ($temaSegment !== null) {
			$segments[] = $temaSegment;
		}

		$segments[] = 'PR' . $pr->number;
		$segments[] = $this->buildVersionSegment($document);
		$segments[] = $this->buildStatusSegment($document);

		return implode('-', $segments);
	}

	/**
	 * Crea un documento y su primera variante dentro de una transaccion.
	 */
	public function createDocument($prId, string $type, $userId, ?int $tema = null): Document
	{
		return DB::transaction(function () use ($prId, $type, $userId, $tema) {
			if ($tema !== null && ! $this->typeSupportsTema($type)) {
				throw new \RuntimeException('El tipo de documento seleccionado no admite tema.');
			}

			$shortTitle = $this->buildShortTitle($type);
			$canonicalName = $this->buildCanonicalName($prId, $type);
			$plantilla = app(PlantillasAction::class)->latestByDocumentTypeOrFail($type);

			$document = Document::create([
				'pr_id' => $prId,
				'plantilla_id' => $plantilla->id,
				'tema' => $tema,
				'type' => $type,
				'short_title' => $shortTitle,
				'canonical_name' => $canonicalName,
			]);

			app(CreateVariantAction::class)->create($document, (int) $userId);

			return $document;
		});
	}

	/**
	 * Genera el titulo corto legible a partir del tipo de documento.
	 *
	 * @param string $type
	 * @return string
	 */
	private function buildShortTitle(string $type): string
	{
		return Str::of(strtolower($type))
			->replace('_', ' ')
			->title()
			->toString();
	}

	/**
	 * Construye el segmento de plantilla del nombre visible del documento.
	 *
	 * @param Document $document
	 * @return string
	 */
	private function buildPlantillaSegment(Document $document): string
	{
		return $document->plantilla?->display_prefijo ?? 'SINPLANTILLA';
	}

	/**
	 * Normaliza el nombre del curso para poder usarlo dentro del nombre del documento.
	 *
	 * @param PR $pr
	 * @return string
	 */
	private function buildCourseSegment(PR $pr): string
	{
		return Str::of($pr->course->name ?? 'SIN_CURSO')
			->ascii()
			->replaceMatches('/\s+/', '_')
			->replaceMatches('/[^A-Za-z0-9_]/', '')
			->trim('_')
			->toString();
	}

	/**
	 * Construye el segmento de tema si el tipo de documento lo admite y existe valor.
	 *
	 * @param Document $document
	 * @return string|null
	 */
	private function buildTemaSegment(Document $document): ?string
	{
		if (! $document->supportsTema() || $document->tema === null) {
			return null;
		}

		return 'Tema' . str_pad((string) $document->tema, 2, '0', STR_PAD_LEFT);
	}

	/**
	 * Construye el segmento de version usando la version de plantilla y la de la ultima variante.
	 *
	 * @param Document $document
	 * @return string
	 */
	private function buildVersionSegment(Document $document): string
	{
		$plantillaVersion = $document->plantilla?->version ?? 0;
		$variantVersion = $document->latestVariant?->version ?? 0;

		return 'V' . $plantillaVersion . '.' . $variantVersion;
	}

	/**
	 * Devuelve la abreviatura del estado actual de la ultima variante.
	 *
	 * @param Document $document
	 * @return string
	 */
	private function buildStatusSegment(Document $document): string
	{
		$statusName = $document->latestVariant?->status?->name;

		return self::STATUS_ABBREVIATIONS[$statusName] ?? 'S';
	}

	/**
	 * Genera un nombre canonico unico para el documento dentro del mismo PR.
	 *
	 * @param int|string $prId
	 * @param string $type
	 * @return string
	 */
	private function buildCanonicalName($prId, string $type): string
	{
		$baseName = Str::of(strtolower($type))->replace('_', '-')->toString();
		$candidate = $baseName . '.pdf';
		$suffix = 2;

		while (Document::where('pr_id', $prId)->where('canonical_name', $candidate)->exists()) {
			$candidate = $baseName . '-' . $suffix . '.pdf';
			$suffix++;
		}

		return $candidate;
	}
}
