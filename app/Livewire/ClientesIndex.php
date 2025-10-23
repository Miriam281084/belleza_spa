<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ClientesIndex extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y control del modal
    public $search = '';
    public $modalAbierto = false;
    public $editando = false;
    public $clienteId;

    // Propiedades del formulario
    public $nombre;
    public $apellido;
    public $dni;
    public $telefono;
    public $email;
    public $fecha_nacimiento;

    protected $paginationTheme = 'tailwind';

    // Resetear la paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('apellido', 'like', "%{$this->search}%")
            ->orWhere('dni', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.clientes-index', [
            'clientes' => $clientes
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
        $this->clienteId = null;
        $this->nombre = '';
        $this->apellido = '';
        $this->dni = '';
        $this->telefono = '';
        $this->email = '';
        $this->fecha_nacimiento = '';
        $this->resetValidation();
    }

    public function guardar()
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:clientes,dni,' . $this->clienteId,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $this->clienteId,
            'fecha_nacimiento' => 'nullable|date|before:today',
        ];

        $this->validate($rules);

        $datos = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'fecha_nacimiento' => $this->fecha_nacimiento,
        ];

        if ($this->clienteId) {
            // Actualizar cliente existente
            $cliente = Cliente::findOrFail($this->clienteId);
            $cliente->update($datos);
            session()->flash('mensaje', 'Cliente actualizado exitosamente.');
        } else {
            // Crear nuevo cliente
            Cliente::create($datos);
            session()->flash('mensaje', 'Cliente creado exitosamente.');
        }

        $this->cerrarModal();
    }

    public function editar($id)
    {
        $cliente = Cliente::findOrFail($id);

        $this->clienteId = $cliente->id;
        $this->nombre = $cliente->nombre;
        $this->apellido = $cliente->apellido;
        $this->dni = $cliente->dni;
        $this->telefono = $cliente->telefono;
        $this->email = $cliente->email;
        $this->fecha_nacimiento = $cliente->fecha_nacimiento?->format('Y-m-d');
        $this->editando = true;
        $this->modalAbierto = true;
    }

    public function eliminar($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        session()->flash('mensaje', 'Cliente eliminado exitosamente.');
    }
}