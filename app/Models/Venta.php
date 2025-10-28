<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id',
        'fecha',
        'monto_total',
        'metodo_pago',
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'detalle_venta')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Calcula el total pagado para esta venta
     */
    public function totalPagado(): float
    {
        return $this->pagos()->where('estado', 'completado')->sum('monto');
    }

    /**
     * Calcula el saldo pendiente de la venta
     */
    public function saldoPendiente(): float
    {
        $totalPagado = $this->totalPagado();
        $saldo = $this->monto_total - $totalPagado;

        return max(0, $saldo); // Nunca retornar valor negativo
    }

    /**
     * Verifica si la venta está completamente pagada
     */
    public function estaPagada(): bool
    {
        return $this->saldoPendiente() <= 0;
    }
}
