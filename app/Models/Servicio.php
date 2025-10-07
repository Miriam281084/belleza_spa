<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }
}
