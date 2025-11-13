<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Productos de Belleza</h1>
            <p class="text-gray-600">Encuentra los mejores productos para tu cuidado personal</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Búsqueda -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar producto</label>
                    <input type="text" wire:model.live="buscar" placeholder="Nombre del producto..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select wire:model.live="filtroCategoria" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Precio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                    <select wire:model.live="filtroPrecio" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <option value="500">Menos de $500</option>
                        <option value="1000">$500 - $1,000</option>
                        <option value="2000">$1,000 - $2,000</option>
                        <option value="2000+">Más de $2,000</option>
                    </select>
                </div>

                <!-- Ordenar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar</label>
                    <select wire:model.live="ordenar" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="nombre_asc">A-Z</option>
                        <option value="nombre_desc">Z-A</option>
                        <option value="precio_asc">Menor precio</option>
                        <option value="precio_desc">Mayor precio</option>
                        <option value="nuevo">Más nuevos</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="soloDisponibles" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Solo productos disponibles</span>
                </label>

                <button wire:click="limpiarFiltros" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Grid de Productos -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
            @forelse($productos as $producto)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                    <!-- Imagen del producto -->
                    <div class="aspect-square relative overflow-hidden bg-gray-100">
                        @if($producto->imagen)
                            <img src="{{ asset($producto->imagen) }}"
                                 alt="{{ $producto->nombre }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                <svg class="w-20 h-20 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        @endif
                        @if($producto->stock < 5 && $producto->stock > 0)
                            <span class="absolute top-2 right-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full shadow-lg">
                                ¡Últimas {{ $producto->stock }}!
                            </span>
                        @elseif($producto->stock == 0)
                            <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow-lg">
                                Agotado
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1 text-sm md:text-base line-clamp-2">
                            {{ $producto->nombre }}
                        </h3>

                        @if($producto->categoria)
                            <span class="inline-block px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded mb-2">
                                {{ $producto->categoria }}
                            </span>
                        @endif

                        <p class="text-xs text-gray-600 mb-3 line-clamp-2">
                            {{ $producto->descripcion }}
                        </p>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xl md:text-2xl font-bold text-purple-600">
                                ${{ number_format($producto->precio, 0) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                Stock: {{ $producto->stock }}
                            </span>
                        </div>

                        <a href="{{ route('mi-carrito') }}"
                            class="block w-full text-center px-3 py-2 text-sm {{ $producto->stock > 0 ? 'bg-purple-600 hover:bg-purple-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }} rounded-md transition">
                            {{ $producto->stock > 0 ? 'Agregar al carrito' : 'No disponible' }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron productos</h3>
                    <p class="mt-2 text-sm text-gray-500">Intenta ajustar los filtros</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $productos->links() }}
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Escuchar eventos de WebSocket para actualizar el catálogo en tiempo real
    if (window.Echo) {
        window.Echo.channel('stock')
            .listen('.stock.actualizado', (event) => {
                console.log('Stock actualizado en catálogo:', event);
                @this.call('$refresh');
            })
            .listen('.producto.agotado', (event) => {
                console.log('Producto agotado en catálogo:', event);
                @this.call('$refresh');
            })
            .listen('.producto.disponible', (event) => {
                console.log('Producto disponible en catálogo:', event);
                @this.call('$refresh');
            });
    }
</script>
@endpush
