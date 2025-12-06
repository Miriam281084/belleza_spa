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

    public $search = '';
    public $modalAbierto = false;
    public $editando = false;
    public $clienteId;

    public $nombre;
    public $apellido;
    public $dni;
    public $fecha_nacimiento;
    public $telefono;
    public $email;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clientes = Cliente::query()
            ->where('nombre', 'like', "%{$this->search}%")
            ->orWhere('apellido', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('dni', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.clientes-index', compact('clientes'));
    }

    public function abrirModal()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) abort(403);

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
        $this->fecha_nacimiento = '';
        $this->telefono = '';
        $this->email = '';
        $this->resetValidation();
    }

    public function guardar()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) abort(403);

        if ($this->clienteId) {

            $rules = [
                'telefono' => 'nullable|string|max:20',
                'email' => 'required|email|max:255|unique:clientes,email,' . $this->clienteId,
            ];

            $this->validate($rules);

            $cliente = Cliente::findOrFail($this->clienteId);
            $cliente->update([
                'telefono' => $this->telefono,
                'email' => $this->email,
            ]);

            $this->dispatch('mostrarMensaje', mensaje: 'Cliente actualizado');
        } else {

            $rules = [
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'dni' => 'required|string|max:50|unique:clientes,dni',
                'fecha_nacimiento' => 'nullable|date',
                'telefono' => 'nullable|string|max:20',
                'email' => 'required|email|max:255|unique:clientes,email',
            ];

            $this->validate($rules);

            Cliente::create([
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'dni' => $this->dni,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'telefono' => $this->telefono,
                'email' => $this->email,
            ]);

            $this->dispatch('mostrarMensaje', mensaje: 'Cliente creado');
        }

        $this->cerrarModal();
    }

    public function editar($id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) abort(403);

        $c = Cliente::findOrFail($id);

        $this->clienteId = $c->id;
        $this->nombre = $c->nombre;
        $this->apellido = $c->apellido;
        $this->dni = $c->dni;
        $this->fecha_nacimiento = $c->fecha_nacimiento;
        $this->telefono = $c->telefono;
        $this->email = $c->email;

        $this->editando = true;
        $this->modalAbierto = true;
    }

    public function eliminar($id)
    {
        abort(403);
    }
}
