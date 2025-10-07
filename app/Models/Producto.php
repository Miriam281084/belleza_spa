<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'categoria',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class, 'detalle_venta')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }
}
