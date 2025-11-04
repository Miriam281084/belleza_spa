<?php

namespace App\Livewire;

use App\Models\Pago;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PagosHistorial extends Component
{
    use WithPagination;

    public $search = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $metodoFiltro = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Pago::with(['cliente', 'turno.servicio', 'venta'])
            ->whereNotNull('turno_id')  // Solo pagos de turnos
            ->whereNull('venta_id')      // Excluir pagos de ventas
            ->orderBy('fecha_pago', 'desc');

        // Filtro por fechas
        if ($this->fechaInicio) {
            $query->whereDate('fecha_pago', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('fecha_pago', '<=', $this->fechaFin);
        }

        // Filtro por método de pago
        if ($this->metodoFiltro) {
            $query->where('metodo_pago', $this->metodoFiltro);
        }

        // Filtro por búsqueda (cliente)
        if ($this->search) {
            $query->whereHas('cliente', function($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $pagos = $query->paginate(15);

        // Calcular estadísticas solo para pagos de turnos
        $totalPagos = Pago::whereNotNull('turno_id')
            ->whereNull('venta_id')
            ->whereDate('fecha_pago', '>=', $this->fechaInicio)
            ->whereDate('fecha_pago', '<=', $this->fechaFin)
            ->sum('monto');

        $totalPorMetodo = Pago::whereNotNull('turno_id')
            ->whereNull('venta_id')
            ->whereDate('fecha_pago', '>=', $this->fechaInicio)
            ->whereDate('fecha_pago', '<=', $this->fechaFin)
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->get();

        return view('livewire.pagos-historial', [
            'pagos' => $pagos,
            'totalPagos' => $totalPagos,
            'totalPorMetodo' => $totalPorMetodo,
        ]);
    }

    public function limpiarFiltros()
    {
        $this->search = '';
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
        $this->metodoFiltro = '';
        $this->resetPage();
    }
}
