<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }
}
