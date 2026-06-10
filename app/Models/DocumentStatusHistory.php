<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStatusHistory extends Model
{
    protected $fillable = ['document_variant_id', 'from_status', 'to_status', 'user_id', 'comment'];
    public function variant()
    {
        return $this->belongsTo(DocumentVariant::class, 'document_variant_id');
    }
}
