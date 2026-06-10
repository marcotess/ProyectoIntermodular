<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['code', 'name'];
    public function prs()
    {
        return $this->hasMany(PR::class);
    }
}
