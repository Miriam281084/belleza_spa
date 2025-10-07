<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
<<<<<<< HEAD
        'telefono',
        'email',
=======
        'dni',
        'telefono',
        'email',
        'especialidad',
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }
}
