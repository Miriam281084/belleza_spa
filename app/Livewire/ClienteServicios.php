<?php

namespace App\Livewire;

use App\Models\Servicio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClienteServicios extends Component
{
    use WithPagination;

    public $buscar = '';
    public $filtroDuracion = '';
    public $filtroPrecio = '';
    public $ordenar = 'nombre_asc';

    protected $paginationTheme = 'tailwind';

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingFiltroDuracion()
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
        $this->filtroDuracion = '';
        $this->filtroPrecio = '';
        $this->ordenar = 'nombre_asc';
        $this->resetPage();
    }

    public function render()
    {
        $query = Servicio::query();

        // Filtro por búsqueda
        if ($this->buscar) {
            $query->where(function($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                  ->orWhere('descripcion', 'like', "%{$this->buscar}%");
            });
        }

        // Filtro por duración
        if ($this->filtroDuracion) {
            switch ($this->filtroDuracion) {
                case '30':
                    $query->where('duracion', '<=', 30);
                    break;
                case '60':
                    $query->whereBetween('duracion', [31, 60]);
                    break;
                case '90':
                    $query->whereBetween('duracion', [61, 90]);
                    break;
                case '120':
                    $query->where('duracion', '>', 90);
                    break;
            }
        }

        // Filtro por precio
        if ($this->filtroPrecio) {
            switch ($this->filtroPrecio) {
                case '1000':
                    $query->where('precio', '<', 1000);
                    break;
                case '2000':
                    $query->whereBetween('precio', [1000, 2000]);
                    break;
                case '3000':
                    $query->whereBetween('precio', [2000, 3000]);
                    break;
                case '3000+':
                    $query->where('precio', '>', 3000);
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
            case 'duracion_asc':
                $query->orderBy('duracion', 'asc');
                break;
            case 'duracion_desc':
                $query->orderBy('duracion', 'desc');
                break;
        }

        $servicios = $query->paginate(12);

        return view('livewire.cliente-servicios', [
            'servicios' => $servicios,
        ]);
    }
}
