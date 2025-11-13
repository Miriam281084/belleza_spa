<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Events\StockActualizado;
use App\Events\ProductoAgotado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class VentasCrear extends Component
{
    use WithPagination;

    // Propiedades principales
    public $id_cliente = '';
    public $productosEnCarrito = [];
    public $montoTotal = 0;
    public $buscarProducto = '';
    public $esCliente = false;
    public $clienteActual = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $user = Auth::user();

        // Verificar si el usuario autenticado tiene rol Cliente
        if ($user->hasRole('Cliente')) {
            $this->esCliente = true;

            // Buscar o crear el perfil de cliente
            $cliente = Cliente::where('user_id', $user->id)->first();

            if (!$cliente) {
                $cliente = Cliente::where('email', $user->email)->first();
                if ($cliente) {
                    $cliente->update(['user_id' => $user->id]);
                }
            }

            if (!$cliente) {
                $nombreCompleto = explode(' ', $user->name, 2);
                $cliente = Cliente::create([
                    'user_id' => $user->id,
                    'nombre' => $nombreCompleto[0] ?? $user->name,
                    'apellido' => $nombreCompleto[1] ?? '',
                    'dni' => null,
                    'email' => $user->email,
                    'telefono' => '',
                ]);
            }

            $this->clienteActual = $cliente;
            $this->id_cliente = $cliente->id;
        }
    }

    // Resetear paginación cuando se busca
    public function updatingBuscarProducto()
    {
        $this->resetPage();
    }

    // Computed property para el monto total
    #[Computed]
    public function calcularTotal()
    {
        $total = 0;
        foreach ($this->productosEnCarrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        $this->montoTotal = $total;
        return $total;
    }

    public function render()
    {
        // Obtener productos disponibles con stock > 0
        $productosDisponibles = Producto::where('stock', '>', 0)
            ->where(function($query) {
                $query->where('nombre', 'like', "%{$this->buscarProducto}%")
                      ->orWhere('categoria', 'like', "%{$this->buscarProducto}%");
            })
            ->orderBy('nombre', 'asc')
            ->paginate(12);

        // Obtener todos los clientes para el select
        $clientes = Cliente::orderBy('nombre', 'asc')->get();

        // Recalcular total
        $this->calcularTotal();

        return view('livewire.ventas-crear', [
            'productosDisponibles' => $productosDisponibles,
            'clientes' => $clientes,
        ]);
    }

    // Agregar producto al carrito
    public function agregarProducto($productoId)
    {
        $producto = Producto::findOrFail($productoId);

        // Verificar si el producto ya está en el carrito
        if (isset($this->productosEnCarrito[$productoId])) {
            // Verificar stock disponible
            if ($this->productosEnCarrito[$productoId]['cantidad'] >= $producto->stock) {
                $this->dispatch('mostrarMensaje',
                    mensaje: 'No hay más stock disponible de este producto.',
                    tipo: 'error'
                );
                return;
            }
            // Incrementar cantidad si ya está
            $this->productosEnCarrito[$productoId]['cantidad']++;
        } else {
            // Agregar nuevo producto con cantidad inicial 1
            $this->productosEnCarrito[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio,
                'cantidad' => 1,
                'stock_disponible' => $producto->stock,
            ];
        }

        $this->dispatch('mostrarMensaje', mensaje: 'Producto agregado al carrito.');
    }

    // Remover producto del carrito
    public function removerProducto($productoId)
    {
        if (isset($this->productosEnCarrito[$productoId])) {
            unset($this->productosEnCarrito[$productoId]);
            $this->dispatch('mostrarMensaje', mensaje: 'Producto removido del carrito.');
        }
    }

    // Actualizar cantidad de producto en el carrito
    public function actualizarCantidad($productoId, $cantidad)
    {
        if (!isset($this->productosEnCarrito[$productoId])) {
            return;
        }

        $cantidad = (int) $cantidad;

        // Validar que la cantidad sea positiva
        if ($cantidad <= 0) {
            $this->removerProducto($productoId);
            return;
        }

        // Validar stock disponible
        $producto = Producto::findOrFail($productoId);
        if ($cantidad > $producto->stock) {
            $this->dispatch('mostrarMensaje',
                mensaje: "Solo hay {$producto->stock} unidades disponibles.",
                tipo: 'error'
            );
            $this->productosEnCarrito[$productoId]['cantidad'] = $producto->stock;
            return;
        }

        $this->productosEnCarrito[$productoId]['cantidad'] = $cantidad;
    }

    // Incrementar cantidad
    public function incrementarCantidad($productoId)
    {
        if (isset($this->productosEnCarrito[$productoId])) {
            $producto = Producto::findOrFail($productoId);
            if ($this->productosEnCarrito[$productoId]['cantidad'] < $producto->stock) {
                $this->productosEnCarrito[$productoId]['cantidad']++;
            } else {
                $this->dispatch('mostrarMensaje',
                    mensaje: 'No hay más stock disponible.',
                    tipo: 'error'
                );
            }
        }
    }

    // Decrementar cantidad
    public function decrementarCantidad($productoId)
    {
        if (isset($this->productosEnCarrito[$productoId])) {
            if ($this->productosEnCarrito[$productoId]['cantidad'] > 1) {
                $this->productosEnCarrito[$productoId]['cantidad']--;
            } else {
                $this->removerProducto($productoId);
            }
        }
    }

    // Vaciar carrito
    public function vaciarCarrito()
    {
        $this->productosEnCarrito = [];
        $this->dispatch('mostrarMensaje', mensaje: 'Carrito vaciado.');
    }

    // Procesar venta con transacción
    public function procesarVenta()
    {
        // Validaciones
        $this->validate([
            'id_cliente' => 'required|exists:clientes,id',
        ], [
            'id_cliente.required' => 'Debe seleccionar un cliente.',
            'id_cliente.exists' => 'El cliente seleccionado no existe.',
        ]);

        // Validar que haya productos en el carrito
        if (empty($this->productosEnCarrito)) {
            $this->dispatch('mostrarMensaje',
                mensaje: 'Debe agregar al menos un producto al carrito.',
                tipo: 'error'
            );
            return;
        }

        try {
            DB::beginTransaction();

            // Crear la venta
            $venta = Venta::create([
                'cliente_id' => $this->id_cliente,
                'fecha' => now(),
                'monto_total' => $this->calcularTotal(),
                'metodo_pago' => 'efectivo', // Por ahora efectivo, luego se integra con pagos
            ]);

            // Crear los detalles de venta y actualizar stock
            foreach ($this->productosEnCarrito as $item) {
                $producto = Producto::findOrFail($item['id']);

                // Verificar stock nuevamente antes de procesar
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }

                // Crear detalle de venta
                $venta->productos()->attach($producto->id, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                ]);

                // Actualizar stock del producto
                $stockAnterior = $producto->stock;
                $producto->stock -= $item['cantidad'];
                $producto->save();

                // Emitir evento de stock actualizado
                broadcast(new StockActualizado($producto, $stockAnterior, $producto->stock))->toOthers();

                // Si el producto quedó agotado, emitir evento
                if ($producto->stock == 0 && $stockAnterior > 0) {
                    broadcast(new ProductoAgotado($producto))->toOthers();
                }
            }

            DB::commit();

            // Limpiar carrito y cliente
            $this->productosEnCarrito = [];
            $this->id_cliente = '';
            $this->montoTotal = 0;

            $this->dispatch('mostrarMensaje',
                mensaje: 'Venta procesada exitosamente. ID de venta: ' . $venta->id
            );

            // Opcional: Redireccionar al historial de ventas
            // return redirect()->route('ventas.historial');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('mostrarMensaje',
                mensaje: 'Error al procesar la venta: ' . $e->getMessage(),
                tipo: 'error'
            );
        }
    }
}
