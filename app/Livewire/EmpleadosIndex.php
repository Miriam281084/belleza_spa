<?php

namespace App\Livewire;

use App\Models\Empleado;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class EmpleadosIndex extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y control del modal
    public $search = '';
    public $modalAbierto = false;
    public $editando = false;
    public $empleadoId;

    // Propiedades del formulario
    public $nombre;
    public $apellido;
    public $telefono;
    public $email;
    public $rol = 'esteticista';

    protected $paginationTheme = 'tailwind';

    // Resetear la paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $empleados = Empleado::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('apellido', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('rol', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.empleados-index', [
            'empleados' => $empleados
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
        $this->empleadoId = null;
        $this->nombre = '';
        $this->apellido = '';
        $this->telefono = '';
        $this->email = '';
        $this->rol = 'esteticista';
        $this->resetValidation();
    }

    public function guardar()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:empleados,email,' . $this->empleadoId,
            'rol' => 'required|in:admin,recepcionista,esteticista',
        ];

        $this->validate($rules);

        $datos = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'rol' => $this->rol,
        ];

        if ($this->empleadoId) {
            // Actualizar empleado existente
            $empleado = Empleado::findOrFail($this->empleadoId);
            $empleado->update($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Empleado actualizado exitosamente.');
        } else {
            // Crear nuevo empleado
            Empleado::create($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Empleado creado exitosamente.');
        }

        $this->cerrarModal();
    }

    public function editar($id)
    {
        $empleado = Empleado::findOrFail($id);

        $this->empleadoId = $empleado->id;
        $this->nombre = $empleado->nombre;
        $this->apellido = $empleado->apellido;
        $this->telefono = $empleado->telefono;
        $this->email = $empleado->email;
        $this->rol = $empleado->rol;
        $this->editando = true;
        $this->modalAbierto = true;
    }

    public function eliminar($id)
    {
        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        $this->dispatch('mostrarMensaje', mensaje: 'Empleado eliminado exitosamente.');
    }
}
