<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Turno extends Model
{
    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'servicio_id',
        'fecha',
        'hora',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
<<<<<<< HEAD
=======
        'hora' => 'time',
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }
}
