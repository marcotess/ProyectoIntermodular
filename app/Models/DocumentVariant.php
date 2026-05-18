<?php

namespace App\Models;

use App\Actions\NotificacionAction;
use Illuminate\Database\Eloquent\Model;

class DocumentVariant extends Model
{
    protected $fillable = ['document_id', 'version', 'status_id', 'deadline_target', 'drive_link_url', 'created_by'];

    protected static function booted(): void
    {
        static::created(function (DocumentVariant $variant): void {
            $variant->dispatchStatusNotification();
        });

        static::updated(function (DocumentVariant $variant): void {
            if (! $variant->wasChanged('status_id')) {
                return;
            }

            $variant->dispatchStatusNotification();
        });
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function status()
    {
        return $this->belongsTo(DocumentStatus::class, 'status_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(DocumentStatusHistory::class);
    }

    private function dispatchStatusNotification(): void
    {
        $this->loadMissing(['document.pr.teachers', 'document.reviewers', 'status']);

        if (! $this->document || ! $this->status) {
            return;
        }

        app(NotificacionAction::class)->notifyDocumentStatus($this->document, $this->status->name);
    }
}
