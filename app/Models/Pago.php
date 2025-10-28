<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'cliente_id',
        'turno_id',
        'monto',
        'metodo_pago',
        'fecha_pago',
        'estado',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }
}
