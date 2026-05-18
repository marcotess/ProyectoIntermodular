<?php

namespace App\Actions;

use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Collection;

class AssignReviewerAction
{
	/**
	 * listar todos los revisores en el select de revisres.
	 * solo listo los revisores que no esten asignados a ese dcumento 
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
	 * asiganarlo,
	 */
	public function assign(Document $document, array $reviewerIds)
	{
		// solo los que sea revisres
		$validIds = User::whereIn('id', $reviewerIds)
			->whereHas('roles', function($q) { $q->where('name', 'revisor'); })
			->pluck('id')->toArray();
		$document->reviewers()->syncWithoutDetaching($validIds);
	}

	/**
	 * quitar al revisor del documento
	 */
	public function remove(Document $document, $reviewerId)
	{
		$document->reviewers()->detach($reviewerId);

		return true;
	}
}
