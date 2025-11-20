<div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

            <!-- Mensaje de éxito/error -->
            <div x-data="{ mensaje: '', mostrar: false, tipo: 'success' }"
                 @mostrar-mensaje.window="mensaje = $event.detail.mensaje; tipo = $event.detail.tipo || 'success'; mostrar = true; setTimeout(() => mostrar = false, 3000)"
                 x-show="mostrar"
                 x-transition
                 :class="tipo === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700'"
                 class="mb-4 border px-3 py-2 md:px-4 md:py-3 rounded relative text-sm md:text-base"
                 style="display: none;">
                <span x-text="mensaje"></span>
            </div>

            <!-- Header responsive -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 md:mb-6 gap-3">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Gestión de Productos</h2>
                <button
                    wire:click="abrirModal"
                    type="button"
                    class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 md:py-2 px-4 rounded transition text-sm md:text-base"
                >
                    + Nuevo Producto
                </button>
            </div>

            <!-- Campo de búsqueda -->
            <div class="mb-4 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar productos..."
                    class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-2.5">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Vista de tarjetas (móvil) -->
            <div class="block md:hidden space-y-4">
                @forelse($productos as $producto)
                    <div wire:key="producto-mobile-{{ $producto->id }}" class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $producto->nombre }}</h3>
                                @if($producto->categoria)
                                    <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                        {{ $producto->categoria }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-lg font-bold text-blue-600">${{ number_format($producto->precio, 0) }}</span>
                        </div>

                        @if($producto->descripcion)
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($producto->descripcion, 60) }}</p>
                        @endif

                        <div class="flex items-center justify-between mb-3 pb-3 border-b">
                            <span class="text-sm text-gray-600">Stock:</span>
                            @if($producto->stock < 5)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $producto->stock }} unidades
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-900">{{ $producto->stock }} unidades</span>
                            @endif
                        </div>

                        <!-- Ajustar stock -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700">Ajustar:</span>
                            <div class="flex items-center space-x-2">
                                <button
                                    wire:click="restarStock({{ $producto->id }})"
                                    wire:loading.attr="disabled"
                                    class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded disabled:opacity-50"
                                >
                                    -
                                </button>
                                <button
                                    wire:click="agregarStock({{ $producto->id }})"
                                    wire:loading.attr="disabled"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold px-3 py-1.5 rounded disabled:opacity-50"
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div>
                            <button
                                wire:click="editar({{ $producto->id }})"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded text-sm"
                            >
                                Editar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        No se encontraron productos.
                    </div>
                @endforelse
            </div>

            <!-- Tabla desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Descripción</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ajustar</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($productos as $producto)
                            <tr wire:key="producto-{{ $producto->id }}">
                                <td class="px-4 lg:px-6 py-3 text-sm font-medium text-gray-900">{{ Str::limit($producto->nombre, 25) }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">{{ $producto->categoria ?? '-' }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $producto->descripcion ? Str::limit($producto->descripcion, 40) : '-' }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">${{ number_format($producto->precio, 0) }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    @if($producto->stock < 5)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $producto->stock }}
                                        </span>
                                    @else
                                        <span class="text-gray-900">{{ $producto->stock }}</span>
                                    @endif
                                </td>
                                <td class="px-4 lg:px-6 py-3">
                                    <div class="flex items-center space-x-1">
                                        <button wire:click="restarStock({{ $producto->id }})" class="bg-red-500 hover:bg-red-600 text-white font-bold px-2 py-1 rounded text-xs">-</button>
                                        <button wire:click="agregarStock({{ $producto->id }})" class="bg-green-500 hover:bg-green-600 text-white font-bold px-2 py-1 rounded text-xs">+</button>
                                    </div>
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    <button wire:click="editar({{ $producto->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No se encontraron productos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $productos->links() }}
            </div>

            <!-- Modal responsive -->
            @if($modalAbierto)
                <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                            <form wire:submit.prevent="guardar">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $editando ? 'Editar Producto' : 'Nuevo Producto' }}</h3>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                            <input type="text" wire:model="nombre_producto" class="w-full px-3 py-2 border rounded-lg @error('nombre_producto') border-red-500 @enderror">
                                            @error('nombre_producto')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                            <input type="text" wire:model="categoria" placeholder="Ej: Cremas, Aceites..." class="w-full px-3 py-2 border rounded-lg">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                            <textarea wire:model="descripcion" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                                    <input type="number" wire:model="precio" min="0" step="0.01" class="w-full pl-7 pr-3 py-2 border rounded-lg @error('precio') border-red-500 @enderror">
                                                </div>
                                                @error('precio')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                                                <input type="number" wire:model="stock" min="0" class="w-full px-3 py-2 border rounded-lg @error('stock') border-red-500 @enderror">
                                                @error('stock')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                                    <button type="button" wire:click="cerrarModal" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                        Cancelar
                                    </button>
                                    <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                        <span wire:loading.remove>{{ $editando ? 'Actualizar' : 'Guardar' }}</span>
                                        <span wire:loading>Guardando...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Escuchar eventos de WebSocket para actualizar el stock en tiempo real
    if (window.Echo) {
        window.Echo.channel('stock')
            .listen('.stock.actualizado', (event) => {
                console.log('Stock actualizado:', event);
                // Recargar el componente Livewire para mostrar cambios
                @this.call('$refresh');

                // Mostrar notificación
                window.dispatchEvent(new CustomEvent('mostrar-mensaje', {
                    detail: {
                        mensaje: `Stock de "${event.nombre}" actualizado: ${event.stock_nuevo} unidades`,
                        tipo: 'success'
                    }
                }));
            })
            .listen('.producto.agotado', (event) => {
                console.log('Producto agotado:', event);
                @this.call('$refresh');

                window.dispatchEvent(new CustomEvent('mostrar-mensaje', {
                    detail: {
                        mensaje: `¡AGOTADO! "${event.nombre}" ya no tiene stock`,
                        tipo: 'error'
                    }
                }));
            })
            .listen('.producto.disponible', (event) => {
                console.log('Producto disponible:', event);
                @this.call('$refresh');

                window.dispatchEvent(new CustomEvent('mostrar-mensaje', {
                    detail: {
                        mensaje: `"${event.nombre}" ahora está disponible con ${event.stock} unidades`,
                        tipo: 'success'
                    }
                }));
            });
    }
</script>
@endpush
