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

    public bool $soloLectura = false;

    // Buscador + modal
    public string $search       = '';
    public bool   $modalAbierto = false;
    public bool   $editando     = false;
    public ?int   $servicioId   = null;

    // Formulario
    public string $nombre_servicio = '';
    public ?string $descripcion    = '';
    public ?int    $duracion       = null;
    public ?float  $precio         = null;
    public string  $estado         = 'Activo';   // Activo | Inactivo

    protected $paginationTheme = 'tailwind';

    // Cuando cambia el término de búsqueda → volver a página 1
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        // Esteticista → solo lectura (no ve botones de crear/editar)
        $this->soloLectura = auth()->check() && auth()->user()->hasRole('Esteticista');

        $servicios = Servicio::query()
            ->where('nombre', 'like', "%{$this->search}%")
            ->orWhere('descripcion', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.servicios-index', [
            'servicios' => $servicios,
        ]);
    }

    public function abrirModal(): void
    {
        $this->asegurarPermiso();


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
        $this->servicioId      = null;
        $this->nombre_servicio = '';
        $this->descripcion     = '';
        $this->duracion        = null;
        $this->precio          = null;
        $this->estado          = 'Activo';
        $this->resetValidation();
    }

    public function guardar(): void
    {
        $this->asegurarPermiso();


        // 📝 EDICIÓN: NO se permite modificar el nombre del servicio
        if ($this->servicioId) {

            $rules = [
                'descripcion' => 'nullable|string',
                'duracion'    => 'required|integer|min:15',
                'precio'      => 'required|numeric|min:0',
                'estado'      => 'required|in:Activo,Inactivo',
            ];

            $this->validate($rules);

            $servicio = Servicio::findOrFail($this->servicioId);

            // Nombre NO se toca
            $servicio->descripcion = $this->descripcion;
            $servicio->duracion    = $this->duracion;
            $servicio->precio      = $this->precio;
            $servicio->estado      = $this->estado;
            $servicio->save();

            $this->dispatch('mostrarMensaje', mensaje: 'Servicio actualizado correctamente.');
        } else {
            // 🆕 CREACIÓN: acá sí se define el nombre
            $rules = [
                'nombre_servicio' => 'required|string|max:255',
                'descripcion'     => 'nullable|string',
                'duracion'        => 'required|integer|min:15',
                'precio'          => 'required|numeric|min:0',
                'estado'          => 'required|in:Activo,Inactivo',
            ];

            $this->validate($rules);

            Servicio::create([
                'nombre'      => $this->nombre_servicio,
                'descripcion' => $this->descripcion,
                'duracion'    => $this->duracion,
                'precio'      => $this->precio,
                'estado'      => $this->estado,
            ]);

            $this->dispatch('mostrarMensaje', mensaje: 'Servicio creado correctamente.');
        }

        $this->cerrarModal();
    }

    public function editar(int $id): void
    {
        $this->asegurarPermiso();


        $servicio = Servicio::findOrFail($id);

        $this->servicioId      = $servicio->id;
        $this->nombre_servicio = $servicio->nombre; // Se muestra pero NO se edita (en el Blade está disabled)
        $this->descripcion     = $servicio->descripcion;
        $this->duracion        = $servicio->duracion;
        $this->precio          = $servicio->precio;
        $this->estado          = $servicio->estado ?? 'Activo';

        $this->editando     = true;
        $this->modalAbierto = true;
    }

    public function eliminar(int $id): void
    {
        $this->asegurarPermiso();


        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        $this->dispatch('mostrarMensaje', mensaje: 'Servicio eliminado exitosamente.');
    }

    /**
     * Helper para asegurar que solo el Admin y recepcionista pueda ejecutar acciones críticas.
     */
    private function asegurarPermiso(): void
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }
    }
}
