<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentNameAlias extends Model
{
    protected $fillable = ['document_id', 'canonical_name', 'created_by'];
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
