<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Nuestros Servicios</h1>
            <p class="text-gray-600">Descubre todos nuestros tratamientos de belleza</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" wire:model.live="buscar" placeholder="Nombre del servicio..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Filtro Duración -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración</label>
                    <select wire:model.live="filtroDuracion" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Todas</option>
                        <option value="30">Hasta 30 min</option>
                        <option value="60">31-60 min</option>
                        <option value="90">61-90 min</option>
                        <option value="120">Más de 90 min</option>
                    </select>
                </div>

                <!-- Filtro Precio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio</label>
                    <select wire:model.live="filtroPrecio" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Todos</option>
                        <option value="1000">Menos de $1,000</option>
                        <option value="2000">$1,000 - $2,000</option>
                        <option value="3000">$2,000 - $3,000</option>
                        <option value="3000+">Más de $3,000</option>
                    </select>
                </div>

                <!-- Ordenar -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                    <select wire:model.live="ordenar" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="nombre_asc">Nombre A-Z</option>
                        <option value="nombre_desc">Nombre Z-A</option>
                        <option value="precio_asc">Precio menor</option>
                        <option value="precio_desc">Precio mayor</option>
                        <option value="duracion_asc">Más rápido</option>
                        <option value="duracion_desc">Más largo</option>
                    </select>
                </div>
            </div>

            <!-- Botón limpiar -->
            <div class="mt-4">
                <button wire:click="limpiarFiltros" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Grid de Servicios -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($servicios as $servicio)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $servicio->nombre }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $servicio->descripcion }}</p>

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center text-gray-500">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm">{{ $servicio->duracion }} minutos</span>
                            </div>
                            <div class="text-2xl font-bold text-purple-600">
                                ${{ number_format($servicio->precio, 0) }}
                            </div>
                        </div>

                        <a href="{{ route('mis-turnos') }}"
                            class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            Reservar Ahora
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron servicios</h3>
                    <p class="mt-2 text-sm text-gray-500">Intenta ajustar los filtros</p>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $servicios->links() }}
        </div>

    </div>
</div>
