<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de rol asignable a usuarios.
 */
// sin roles todo esto se nos caeria rapido, asi que aunque sea simple mejor dejarlo bien claro.
class Role extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
