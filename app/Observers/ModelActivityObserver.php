<?php

namespace App\Observers;

use App\Support\UserActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer genérico para auditar modelos del dominio.
 *
 * 
 */
class ModelActivityObserver
{
    public function __construct(private readonly UserActivityLogger $activityLogger)
    {
    }
    public function created(Model $model): void
    {
        $this->activityLogger->logModel('model.created', $model, [
            'attributes' => $this->activityLogger->sanitizedAttributes($model->getAttributes()),
        ]);
    }

    public function updated(Model $model): void
    {
        $changes = $this->activityLogger->diffForUpdate($model);

        if ($changes === []) {
            return;
        }

        $this->activityLogger->logModel('model.updated', $model, [
            'changes' => $changes,
        ]);
    }

    /**
     * 
     */
    public function deleted(Model $model): void
    {
        $this->activityLogger->logModel('model.deleted', $model, [
            'attributes' => $this->activityLogger->sanitizedAttributes($model->getOriginal()),
        ]);
    }
}