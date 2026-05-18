<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PRTeacher extends Model
{
    protected $table = 'pr_teachers';

    protected $fillable = [
        'pr_id',
        'user_id',
    ];

    // n docente no debe aparecer dos veces en la misma PR
    // 

    public function pr()
    {
        return $this->belongsTo(PR::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
