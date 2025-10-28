<?php

namespace App\Livewire;

use App\Models\Servicio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ServiciosIndex extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y control del modal
    public $search = '';
    public $modalAbierto = false;
    public $editando = false;
    public $servicioId;

    // Propiedades del formulario
    public $nombre_servicio;
    public $descripcion;
    public $duracion;
    public $precio;

    protected $paginationTheme = 'tailwind';

    // Resetear la paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $servicios = Servicio::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('descripcion', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.servicios-index', [
            'servicios' => $servicios
        ]);
    }

    public function abrirModal()
    {
        $this->resetearFormulario();
        $this->modalAbierto = true;
        $this->editando = false;
    }

    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
    }

    private function resetearFormulario()
    {
        $this->servicioId = null;
        $this->nombre_servicio = '';
        $this->descripcion = '';
        $this->duracion = '';
        $this->precio = '';
        $this->resetValidation();
    }

    public function guardar()
    {
        $rules = [
            'nombre_servicio' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'required|integer|min:15',
            'precio' => 'required|numeric|min:0',
        ];

        $this->validate($rules);

        $datos = [
            'nombre' => $this->nombre_servicio,
            'descripcion' => $this->descripcion,
            'duracion' => $this->duracion,
            'precio' => $this->precio,
        ];

        if ($this->servicioId) {
            // Actualizar servicio existente
            $servicio = Servicio::findOrFail($this->servicioId);
            $servicio->update($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Servicio actualizado exitosamente.');
        } else {
            // Crear nuevo servicio
            Servicio::create($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Servicio creado exitosamente.');
        }

        $this->cerrarModal();
    }

    public function editar($id)
    {
        $servicio = Servicio::findOrFail($id);

        $this->servicioId = $servicio->id;
        $this->nombre_servicio = $servicio->nombre;
        $this->descripcion = $servicio->descripcion;
        $this->duracion = $servicio->duracion;
        $this->precio = $servicio->precio;
        $this->editando = true;
        $this->modalAbierto = true;
    }

    public function eliminar($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        $this->dispatch('mostrarMensaje', mensaje: 'Servicio eliminado exitosamente.');
    }
}
