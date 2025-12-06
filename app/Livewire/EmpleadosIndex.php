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

    // Búsqueda y modal
    public string $search       = '';
    public bool   $modalAbierto = false;
    public bool   $editando     = false;
    public ?int   $empleadoId   = null;

    // Formulario
    public string $nombre   = '';
    public string $apellido = '';
    public string $telefono = '';
    public string $email    = '';
    public string $rol      = 'esteticista';   // admin | recepcionista | esteticista | masajista | manicurista
    public string $estado   = 'Activo';        // Activo | Inactivo

    // Para la vista (mostrar/ocultar botones)
    public bool $esAdmin = false;

    protected $paginationTheme = 'tailwind';

    /**
     * Roles permitidos (deben coincidir con el ENUM de la BD)
     */
    private array $rolesPermitidos = [
        'admin',
        'recepcionista',
        'esteticista',
        'masajista',
        'manicurista',
    ];

    /* ------------------- Ciclo de vida ------------------- */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->esAdmin = auth()->check() && auth()->user()->hasRole('Admin');

        $empleados = Empleado::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('apellido', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->orWhere('rol', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.empleados-index', compact('empleados'));
    }

    /* ------------------- Modal ------------------- */

    public function abrirModal(): void
    {
        $this->asegurarAdmin();

        $this->resetearFormulario();
        $this->modalAbierto = true;
        $this->editando     = false;
    }

    public function cerrarModal(): void
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
    }

    private function resetearFormulario(): void
    {
        $this->empleadoId = null;
        $this->nombre     = '';
        $this->apellido   = '';
        $this->telefono   = '';
        $this->email      = '';
        $this->rol        = 'esteticista';
        $this->estado     = 'Activo';

        $this->resetValidation();
    }

    /* ------------------- Guardar ------------------- */

    public function guardar(): void
    {
        $this->asegurarAdmin();

        $roles = implode(',', $this->rolesPermitidos);

        if ($this->empleadoId) {
            // EDICIÓN
            $rules = [
                'telefono' => 'nullable|string|max:20',
                'email'    => 'required|email|max:255|unique:empleados,email,' . $this->empleadoId,
                'rol'      => "required|in:{$roles}",
                'estado'   => 'required|in:Activo,Inactivo',
            ];
        } else {
            // CREACIÓN
            $rules = [
                'nombre'   => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'email'    => 'required|email|max:255|unique:empleados,email',
                'rol'      => "required|in:{$roles}",
                'estado'   => 'required|in:Activo,Inactivo',
            ];
        }

        $this->validate($rules);

        if ($this->empleadoId) {
            // Actualizar empleado
            $empleado = Empleado::findOrFail($this->empleadoId);

            // No se tocan nombre ni apellido
            $empleado->telefono = $this->telefono;
            $empleado->email    = $this->email;
            $empleado->rol      = $this->rol;
            $empleado->estado   = $this->estado;
            $empleado->save();

            $this->dispatch('mostrarMensaje', mensaje: 'Empleado actualizado exitosamente.');
        } else {
            // Crear empleado
            Empleado::create([
                'nombre'   => $this->nombre,
                'apellido' => $this->apellido,
                'telefono' => $this->telefono,
                'email'    => $this->email,
                'rol'      => $this->rol,
                'estado'   => $this->estado,
            ]);

            $this->dispatch('mostrarMensaje', mensaje: 'Empleado creado exitosamente.');
        }

        $this->cerrarModal();
    }

    /* ------------------- Otras acciones ------------------- */

    public function editar(int $id): void
    {
        $this->asegurarAdmin();

        $empleado = Empleado::findOrFail($id);

        $this->empleadoId = $empleado->id;
        $this->nombre     = $empleado->nombre;
        $this->apellido   = $empleado->apellido;
        $this->telefono   = $empleado->telefono;
        $this->email      = $empleado->email;
        $this->rol        = $empleado->rol;
        $this->estado     = $empleado->estado ?? 'Activo';

        $this->editando     = true;
        $this->modalAbierto = true;
    }

    public function eliminar(int $id): void
    {
        $this->asegurarAdmin();

        $empleado = Empleado::findOrFail($id);
        $empleado->delete();

        $this->dispatch('mostrarMensaje', mensaje: 'Empleado eliminado exitosamente.');
    }

    /* ------------------- Helper ------------------- */

    private function asegurarAdmin(): void
    {
        if (! auth()->check() || ! auth()->user()->hasRole('Admin')) {
            abort(403);
        }
    }
}

