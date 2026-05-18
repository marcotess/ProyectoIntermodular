<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\DocumentNameAlias;
use App\Models\DocumentStatus;
use App\Models\DocumentStatusHistory;
use App\Models\DocumentVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UpdateDocumentAction
{
	public function listVariantStatuses(): Collection
	{
		DocumentStatus::ensureDefaults();

		return DocumentStatus::query()
			->orderBy('name')
			->get();
	}

	public function updateVariantStatus($variantId, $statusId)
	{
		return DB::transaction(function () use ($variantId, $statusId) {
			$variant = DocumentVariant::with(['document.pr.teachers', 'document.reviewers'])->findOrFail($variantId);
			$status = DocumentStatus::findOrFail($statusId);

			if ((int) $variant->status_id === (int) $status->id) {
				return true;
			}
			// Valida que el nuevo estado siga cumpliendo las reglas del documento.
			app(EnsureVariantStatusAvailabilityAction::class)->statusRules($variant->document_id, $status->name, $variant->id);

			$variant->status_id = $status->id;
			$variant->save();

			//Mueve el archivo a la carpeta del nuevo estado
			app(DocumentFilesystemAction::class)->moveVariantFile($variant->fresh('document.pr.course', 'status'));

			return true;
		});
	}

	public function updateTema(int $documentId, ?int $tema): Document
	{
		return DB::transaction(function () use ($documentId, $tema) {
			$document = Document::findOrFail($documentId);

			if ($tema !== null && ! $document->supportsTema()) {
				throw new RuntimeException('Este tipo de documento no admite tema.');
			}

			$document->tema = $tema;
			$document->save();

			return $document->fresh();
		});
	}




    
	public function removeVariant($variantId)
	{
		return DB::transaction(function () use ($variantId) {
			$variant = DocumentVariant::findOrFail($variantId);

			DocumentStatusHistory::where('document_variant_id', $variant->id)->delete();
			// borra tambien el archivo  (si existe))
			app(DocumentFilesystemAction::class)->deleteVariantFile($variant);
			$variant->delete();

			return true;
		});
	}

	public function remove($documentId)
	{
		return DB::transaction(function () use ($documentId) {
			$document = Document::with('variants')->findOrFail($documentId);

			if ($document->variants->isNotEmpty()) {
				return false;
			}

			$document->reviewers()->detach();
			DocumentNameAlias::where('document_id', $document->id)->delete();
			$document->delete();

			return true;
		});
	}
}
