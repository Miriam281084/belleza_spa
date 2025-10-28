<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    use HasFactory;
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

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Calcula el total pagado para este turno
     */
    public function totalPagado(): float
    {
        return $this->pagos()->where('estado', 'completado')->sum('monto');
    }

    /**
     * Calcula el saldo pendiente del turno
     */
    public function saldoPendiente(): float
    {
        $precioServicio = $this->servicio ? $this->servicio->precio : 0;
        $totalPagado = $this->totalPagado();
        $saldo = $precioServicio - $totalPagado;

        return max(0, $saldo); // Nunca retornar valor negativo
    }

    /**
     * Verifica si el turno está completamente pagado
     */
    public function estaPagado(): bool
    {
        return $this->saldoPendiente() <= 0;
    }
}
