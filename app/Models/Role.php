<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de rol asignable a usuarios.
 */
class Role extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
