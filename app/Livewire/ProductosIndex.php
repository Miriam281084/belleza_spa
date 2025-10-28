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

    // Propiedades de búsqueda y control del modal
    public $search = '';
    public $modalAbierto = false;
    public $editando = false;
    public $productoId;

    // Propiedades del formulario
    public $nombre_producto;
    public $descripcion;
    public $precio;
    public $stock;
    public $categoria;

    protected $paginationTheme = 'tailwind';

    // Resetear la paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $productos = Producto::where('nombre', 'like', "%{$this->search}%")
            ->orWhere('descripcion', 'like', "%{$this->search}%")
            ->orWhere('categoria', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.productos-index', [
            'productos' => $productos
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
        $this->productoId = null;
        $this->nombre_producto = '';
        $this->descripcion = '';
        $this->precio = '';
        $this->stock = 0;
        $this->categoria = '';
        $this->resetValidation();
    }

    public function guardar()
    {
        $rules = [
            'nombre_producto' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categoria' => 'nullable|string|max:100',
        ];

        $this->validate($rules);

        $datos = [
            'nombre' => $this->nombre_producto,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria' => $this->categoria,
        ];

        if ($this->productoId) {
            // Actualizar producto existente
            $producto = Producto::findOrFail($this->productoId);
            $producto->update($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Producto actualizado exitosamente.');
        } else {
            // Crear nuevo producto
            Producto::create($datos);
            $this->dispatch('mostrarMensaje', mensaje: 'Producto creado exitosamente.');
        }

        $this->cerrarModal();
    }

    public function editar($id)
    {
        $producto = Producto::findOrFail($id);

        $this->productoId = $producto->id;
        $this->nombre_producto = $producto->nombre;
        $this->descripcion = $producto->descripcion;
        $this->precio = $producto->precio;
        $this->stock = $producto->stock;
        $this->categoria = $producto->categoria;
        $this->editando = true;
        $this->modalAbierto = true;
    }

    public function eliminar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        $this->dispatch('mostrarMensaje', mensaje: 'Producto eliminado exitosamente.');
    }

    // Método para agregar stock manualmente
    public function agregarStock($productoId, $cantidad = 1)
    {
        $producto = Producto::findOrFail($productoId);
        $producto->stock += $cantidad;
        $producto->save();

        $this->dispatch('mostrarMensaje', mensaje: "Stock agregado correctamente. Nuevo stock: {$producto->stock}");
    }

    // Método para restar stock manualmente con validación de que no quede negativo
    public function restarStock($productoId, $cantidad = 1)
    {
        $producto = Producto::findOrFail($productoId);

        if ($producto->stock - $cantidad < 0) {
            $this->dispatch('mostrarMensaje', mensaje: 'No se puede restar más stock del disponible.', tipo: 'error');
            return;
        }

        $producto->stock -= $cantidad;
        $producto->save();

        $this->dispatch('mostrarMensaje', mensaje: "Stock restado correctamente. Nuevo stock: {$producto->stock}");
    }
}
