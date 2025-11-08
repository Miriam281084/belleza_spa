<div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

            <!-- Panel izquierdo: Productos disponibles -->
            <div class="lg:col-span-2 order-2 lg:order-1">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Productos Disponibles</h2>

                    <!-- Buscador de productos -->
                    <div class="mb-4 md:mb-6 relative">
                        <input
                            type="text"
                            wire:model.live="buscarProducto"
                            placeholder="Buscar productos..."
                            class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                        >
                        <div wire:loading wire:target="buscarProducto" class="absolute right-3 top-2 md:top-3">
                            <svg class="animate-spin h-4 w-4 md:h-5 md:w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Grilla de productos -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-3 md:gap-4 mb-4 md:mb-6">
                        @forelse($productosDisponibles as $producto)
                            <div wire:key="producto-{{ $producto->id }}"
                                 class="border border-gray-200 rounded-lg p-3 md:p-4 hover:shadow-md transition">
                                <div class="flex flex-col h-full">
                                    <div class="flex-grow">
                                        <h3 class="font-semibold text-gray-900 mb-1 text-sm md:text-base">{{ $producto->nombre }}</h3>
                                        @if($producto->categoria)
                                            <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded mb-2">
                                                {{ $producto->categoria }}
                                            </span>
                                        @endif
                                        <p class="text-xs md:text-sm text-gray-600 mb-2">
                                            {{ $producto->descripcion ? Str::limit($producto->descripcion, 50) : '-' }}
                                        </p>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-base md:text-lg font-bold text-blue-600">${{ number_format($producto->precio, 0) }}</span>
                                            <span class="text-xs md:text-sm {{ $producto->stock < 5 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                Stock: {{ $producto->stock }}
                                            </span>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="agregarProducto({{ $producto->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="agregarProducto({{ $producto->id }})"
                                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded transition disabled:opacity-50 text-xs md:text-sm"
                                    >
                                        <span wire:loading.remove wire:target="agregarProducto({{ $producto->id }})">
                                            Agregar
                                        </span>
                                        <span wire:loading wire:target="agregarProducto({{ $producto->id }})">
                                            Agregando...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                No se encontraron productos disponibles.
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    <div>
                        {{ $productosDisponibles->links() }}
                    </div>

                </div>
            </div>

            <!-- Panel derecho: Carrito de compras -->
            <div class="lg:col-span-1 order-1 lg:order-2">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6 lg:sticky lg:top-4">

                    <div class="flex justify-between items-center mb-4 md:mb-6">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Carrito</h2>
                        @if(count($productosEnCarrito) > 0)
                            <button
                                wire:click="vaciarCarrito"
                                wire:confirm="¿Está seguro de vaciar el carrito?"
                                class="text-xs md:text-sm text-red-600 hover:text-red-800"
                            >
                                Vaciar
                            </button>
                        @endif
                    </div>

                    <!-- Selección de cliente -->
                    <div class="mb-4 md:mb-6">
                        <label for="id_cliente" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">
                            Cliente *
                            @if(!empty($productosEnCarrito) && empty($id_cliente) && !$esCliente)
                                <span class="text-red-600 text-xs font-normal">(Requerido para continuar)</span>
                            @endif
                        </label>

                        @if($esCliente && $clienteActual)
                            <!-- Mostrar nombre del cliente autenticado -->
                            <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm md:text-base">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $clienteActual->nombre }} {{ $clienteActual->apellido }}</span>
                                </div>
                            </div>
                        @else
                            <!-- Selector de cliente para empleados -->
                            <select
                                id="id_cliente"
                                wire:model.live="id_cliente"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base @error('id_cliente') border-red-500 @enderror
                                @if(!empty($productosEnCarrito) && empty($id_cliente)) border-amber-500 ring-2 ring-amber-200 @endif"
                            >
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                                @endforeach
                            </select>
                        @endif

                        @error('id_cliente')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Lista de productos en el carrito -->
                    <div class="mb-4 md:mb-6 max-h-64 md:max-h-96 overflow-y-auto">
                        @forelse($productosEnCarrito as $productoId => $item)
                            <div wire:key="carrito-{{ $productoId }}"
                                 class="border-b border-gray-200 py-2 md:py-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-grow pr-2">
                                        <h4 class="font-semibold text-gray-900 text-xs md:text-sm">{{ Str::limit($item['nombre'], 25) }}</h4>
                                        <p class="text-xs text-gray-500">${{ number_format($item['precio'], 0) }} c/u</p>
                                    </div>
                                    <button
                                        wire:click="removerProducto({{ $productoId }})"
                                        class="text-red-500 hover:text-red-700 text-xs"
                                    >
                                        ✕
                                    </button>
                                </div>

                                <!-- Control de cantidad -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1 md:space-x-2">
                                        <button
                                            wire:click="decrementarCantidad({{ $productoId }})"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-2 py-1 rounded text-xs md:text-sm"
                                        >
                                            -
                                        </button>
                                        <input
                                            type="number"
                                            wire:change="actualizarCantidad({{ $productoId }}, $event.target.value)"
                                            value="{{ $item['cantidad'] }}"
                                            min="1"
                                            max="{{ $item['stock_disponible'] }}"
                                            class="w-12 md:w-16 text-center border border-gray-300 rounded px-1 md:px-2 py-1 text-xs md:text-sm"
                                        >
                                        <button
                                            wire:click="incrementarCantidad({{ $productoId }})"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-2 py-1 rounded text-xs md:text-sm"
                                        >
                                            +
                                        </button>
                                    </div>
                                    <div class="font-semibold text-gray-900 text-sm md:text-base">
                                        ${{ number_format($item['precio'] * $item['cantidad'], 0) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 md:py-8 text-gray-500 text-sm">
                                El carrito está vacío
                            </div>
                        @endforelse
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-300 pt-3 md:pt-4 mb-4 md:mb-6">
                        <div class="flex justify-between items-center mb-1 md:mb-2">
                            <span class="text-base md:text-lg font-semibold text-gray-700">Total:</span>
                            <span class="text-xl md:text-2xl font-bold text-blue-600">
                                ${{ number_format($montoTotal, 0) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 text-right">
                            {{ count($productosEnCarrito) }} producto(s)
                        </p>
                    </div>

                    <!-- Botón procesar venta -->
                    <button
                        wire:click="procesarVenta"
                        wire:loading.attr="disabled"
                        wire:target="procesarVenta"
                        {{ (empty($productosEnCarrito) || empty($id_cliente)) ? 'disabled' : '' }}
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 md:py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed text-sm md:text-base"
                    >
                        <span wire:loading.remove wire:target="procesarVenta">
                            Procesar Venta
                        </span>
                        <span wire:loading wire:target="procesarVenta">
                            Procesando...
                        </span>
                    </button>

                    @if(empty($productosEnCarrito))
                        <p class="text-xs text-center text-amber-600 mt-2 font-medium">
                            📦 Primero agregue productos al carrito
                        </p>
                    @elseif(empty($id_cliente))
                        <p class="text-xs text-center text-amber-600 mt-2 font-medium">
                            👤 Seleccione un cliente para procesar la venta
                        </p>
                    @else
                        <p class="text-xs text-center text-green-600 mt-2 font-medium">
                            ✅ ¡Listo para procesar!
                        </p>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
