<?php

namespace App\Livewire;

use App\Models\Venta;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VentasHistorial extends Component
{
    use WithPagination;

    public $search = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $ventaSeleccionada = null;
    public $mostrarDetalle = false;

    protected $paginationTheme = 'tailwind';

    // Resetear paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        // Establecer fecha de inicio como hace 30 días por defecto
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Venta::with(['cliente', 'productos', 'pagos'])
            ->orderBy('fecha', 'desc');

        // Filtro por fechas
        if ($this->fechaInicio) {
            $query->whereDate('fecha', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha', '<=', $this->fechaFin);
        }

        // Filtro por búsqueda (cliente)
        if ($this->search) {
            $query->whereHas('cliente', function($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $ventas = $query->paginate(15);

        return view('livewire.ventas-historial', [
            'ventas' => $ventas
        ]);
    }

    public function verDetalle($ventaId)
    {
        $this->ventaSeleccionada = Venta::with(['cliente', 'productos', 'pagos'])->findOrFail($ventaId);
        $this->mostrarDetalle = true;
    }

    public function cerrarDetalle()
    {
        $this->mostrarDetalle = false;
        $this->ventaSeleccionada = null;
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
        $this->resetPage();
    }
}
