<?php

namespace App\Livewire;

use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductosIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $modalAbierto = false;
    public $editando = false;

    public $productoId;

    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $categoria;
    public $estado = 'Activo';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $productos = Producto::query()
            ->where('nombre', 'like', "%{$this->search}%")
            ->orWhere('descripcion', 'like', "%{$this->search}%")
            ->orWhere('categoria', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.productos-index', compact('productos'));
    }

    // -------------------------------------------------------------------------
    // ABRIR MODAL (Admin + Recepcionista)
    // -------------------------------------------------------------------------
    public function abrirModal()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }

        $this->resetearFormulario();
        $this->modalAbierto = true;
        $this->editando = false;
    }

    // -------------------------------------------------------------------------
    // CERRAR MODAL
    // -------------------------------------------------------------------------
    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
    }

    // -------------------------------------------------------------------------
    private function resetearFormulario()
    {
        $this->productoId = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->precio = '';
        $this->stock = 0;
        $this->categoria = '';
        $this->estado = 'Activo';
        $this->resetValidation();
    }

    // -------------------------------------------------------------------------
    // GUARDAR (CREAR Y EDITAR) - Admin + Recepcionista
    // -------------------------------------------------------------------------
    public function guardar()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }

        $rules = [
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:150',
            'estado' => 'required|in:Activo,Inactivo',
        ];

        if (!$this->editando) {
            // Nombre solo al crear
            $rules['nombre'] = 'required|string|max:255|unique:productos,nombre';
        }

        $this->validate($rules);

        $datos = [
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria' => $this->categoria,
            'estado' => $this->estado
        ];

        if ($this->editando) {
            // Editar producto
            $producto = Producto::findOrFail($this->productoId);
            $producto->update($datos);

            $this->dispatch('mostrarMensaje', mensaje: 'Producto actualizado.');
        } else {
            // Crear producto
            $datos['nombre'] = $this->nombre;
            Producto::create($datos);

            $this->dispatch('mostrarMensaje', mensaje: 'Producto creado.');
        }

        $this->cerrarModal();
    }

    // -------------------------------------------------------------------------
    // EDITAR
    // -------------------------------------------------------------------------
    public function editar($id)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }

        $producto = Producto::findOrFail($id);

        $this->productoId = $producto->id;
        $this->nombre = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->precio = $producto->precio;
        $this->stock = $producto->stock;
        $this->categoria = $producto->categoria;
        $this->estado = $producto->estado;

        $this->editando = true;
        $this->modalAbierto = true;
    }

    // -------------------------------------------------------------------------
    // ELIMINAR (DESHABILITADO)
    // -------------------------------------------------------------------------
    public function eliminar($id)
    {
        abort(403);
    }

    // -------------------------------------------------------------------------
    // AGREGAR STOCK
    // -------------------------------------------------------------------------
    public function agregarStock($productoId, $cantidad = 1)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }

        $producto = Producto::findOrFail($productoId);
        $producto->stock += $cantidad;
        $producto->save();

        $this->dispatch('mostrarMensaje', mensaje: 'Stock agregado.');
    }

    // -------------------------------------------------------------------------
    // RESTAR STOCK
    // -------------------------------------------------------------------------
    public function restarStock($productoId, $cantidad = 1)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Recepcionista'])) {
            abort(403);
        }

        $producto = Producto::findOrFail($productoId);

        if ($producto->stock - $cantidad < 0) {
            $this->dispatch('mostrarMensaje', mensaje: 'No se puede restar más stock del disponible.', tipo: 'error');
            return;
        }

        $producto->stock -= $cantidad;
        $producto->save();

        $this->dispatch('mostrarMensaje', mensaje: 'Stock restado.');
    }
}
