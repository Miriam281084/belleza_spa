<?php

namespace App\Livewire;

use App\Models\Servicio;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClienteInicio extends Component
{
    public function render()
    {
        // Servicios destacados (los 6 más populares o todos si son menos)
        $serviciosDestacados = Servicio::orderBy('precio', 'desc')
            ->limit(6)
            ->get();

        // Productos destacados
        $productosDestacados = Producto::where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('livewire.cliente-inicio', [
            'serviciosDestacados' => $serviciosDestacados,
            'productosDestacados' => $productosDestacados,
        ]);
    }
}
