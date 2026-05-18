<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PR extends Model
{
    protected $table = 'prs';

    public const DEFAULT_FASE = 'Temario preliminar';

    public const PHASES = [
        self::DEFAULT_FASE,
        'Temario final',
        'Generación de contenidos',
        'Generación de contenidos y vídeos',
        'Generación de vídeos',
        'Finalizado',
    ];

    protected $fillable = ['course_id', 'number', 'deadline', 'fecha_limite', 'fase'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'pr_teachers', 'pr_id', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'pr_id');
    }
}
