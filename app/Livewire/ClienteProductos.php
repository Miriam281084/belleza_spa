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

    public $buscar = '';
    public $filtroCategoria = '';
    public $filtroPrecio = '';
    public $ordenar = 'nombre_asc';
    public $soloDisponibles = true;

    protected $paginationTheme = 'tailwind';

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingFiltroCategoria()
    {
        $this->resetPage();
    }

    public function updatingFiltroPrecio()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->buscar = '';
        $this->filtroCategoria = '';
        $this->filtroPrecio = '';
        $this->ordenar = 'nombre_asc';
        $this->soloDisponibles = true;
        $this->resetPage();
    }

    public function getCategoriasProperty()
    {
        return Producto::select('categoria')
            ->distinct()
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->pluck('categoria');
    }

    public function render()
    {
        $query = Producto::query();

        // Solo productos con stock
        if ($this->soloDisponibles) {
            $query->where('stock', '>', 0);
        }

        // Filtro por búsqueda
        if ($this->buscar) {
            $query->where(function($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$this->buscar}%");
            });
        }

        // Filtro por categoría
        if ($this->filtroCategoria) {
            $query->where('categoria', $this->filtroCategoria);
        }

        // Filtro por precio
        if ($this->filtroPrecio) {
            switch ($this->filtroPrecio) {
                case '500':
                    $query->where('precio', '<', 500);
                    break;
                case '1000':
                    $query->whereBetween('precio', [500, 1000]);
                    break;
                case '2000':
                    $query->whereBetween('precio', [1000, 2000]);
                    break;
                case '2000+':
                    $query->where('precio', '>', 2000);
                    break;
            }
        }

        // Ordenamiento
        switch ($this->ordenar) {
            case 'nombre_asc':
                $query->orderBy('nombre', 'asc');
                break;
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
        }

        $productos = $query->paginate(12);

        return view('livewire.cliente-productos', [
            'productos' => $productos,
            'categorias' => $this->categorias,
        ]);
    }
}
