<?php

namespace App\Livewire;

use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClienteProductos extends Component
{
    use WithPagination;

    // Filtros y búsqueda
    public $buscar           = '';
    public $filtroCategoria  = '';
    public $filtroPrecio     = '';
    public $ordenar          = 'nombre_asc';
    public $soloDisponibles  = false;

    protected $paginationTheme = 'tailwind';

    // Cuando cambia cualquier filtro, volvemos a la página 1
    public function updatingBuscar()          { $this->resetPage(); }
    public function updatingFiltroCategoria() { $this->resetPage(); }
    public function updatingFiltroPrecio()    { $this->resetPage(); }
    public function updatingOrdenar()         { $this->resetPage(); }
    public function updatingSoloDisponibles() { $this->resetPage(); }

    public function limpiarFiltros()
    {
        $this->buscar          = '';
        $this->filtroCategoria = '';
        $this->filtroPrecio    = '';
        $this->ordenar         = 'nombre_asc';
        $this->soloDisponibles = false;
        $this->resetPage();
    }

    public function render()
    {
        // Categorías solo de productos activos
        $categorias = Producto::where('estado', 'Activo')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->filter() // quita null / vacíos
            ->values();

        $query = Producto::query()
            ->where('estado', 'Activo');

        // Buscar por nombre o descripción
        if (!empty($this->buscar)) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$this->buscar}%");
            });
        }

        // Filtro por categoría
        if (!empty($this->filtroCategoria)) {
            $query->where('categoria', $this->filtroCategoria);
        }

        // Filtro por precio
        if (!empty($this->filtroPrecio)) {
            switch ($this->filtroPrecio) {
                case '500':       // Menos de 500
                    $query->where('precio', '<', 500);
                    break;
                case '1000':      // 500 - 1000
                    $query->whereBetween('precio', [500, 1000]);
                    break;
                case '2000':      // 1000 - 2000
                    $query->whereBetween('precio', [1000, 2000]);
                    break;
                case '2000+':     // Más de 2000
                    $query->where('precio', '>', 2000);
                    break;
            }
        }

        // Solo productos con stock > 0
        if ($this->soloDisponibles) {
            $query->where('stock', '>', 0);
        }

        // Ordenamiento
        switch ($this->ordenar) {
            case 'nombre_desc':
                $query->orderBy('nombre', 'desc');
                break;
            case 'precio_asc':
                $query->orderBy('precio', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('precio', 'desc');
                break;
            case 'nuevo':
                $query->orderBy('created_at', 'desc');
                break;
            case 'nombre_asc':
            default:
                $query->orderBy('nombre', 'asc');
                break;
        }

        $productos = $query->paginate(12);

        return view('livewire.cliente-productos', [
            'productos'  => $productos,
            'categorias' => $categorias,
        ]);
    }
}
