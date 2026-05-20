<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Collection;

class AssignReviewerAction
{
	/**
	 * Devuelve los revisores disponibles para asignar a un documento.
	 *
	 * Se excluyen los usuarios que ya estan asociados al documento.
	 */
	public function availableForDocument(Document $document): Collection
	{
		$assignedReviewerIds = $document->reviewers()->pluck('users.id');

		return User::whereHas('roles', function ($query) {
			$query->where('name', 'revisor');
		})
			->whereNotIn('id', $assignedReviewerIds)
			->orderBy('name')
			->get();
	}

	/**
	 * Asigna uno o varios revisores validos al documento.
	 */
	public function assign(Document $document, array $reviewerIds)
	{
		// Solo se vinculan usuarios que tengan rol de revisor.
		$validIds = User::whereIn('id', $reviewerIds)
			->whereHas('roles', function($q) { $q->where('name', 'revisor'); })
			->pluck('id')->toArray();
		$document->reviewers()->syncWithoutDetaching($validIds);
	}

	/**
	 * Elimina la asignacion de un revisor sobre el documento.
	 */
	public function remove(Document $document, $reviewerId)
	{
		$document->reviewers()->detach($reviewerId);

		return true;
	}
}
