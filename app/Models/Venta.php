<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
