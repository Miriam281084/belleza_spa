<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'email',
        'fecha_nacimiento',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Relación con el usuario autenticado (opcional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }
}