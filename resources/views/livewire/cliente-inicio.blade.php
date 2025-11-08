<div class="py-12 bg-gradient-to-b from-purple-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Bienvenido a Belleza Spa Victoria
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Tu espacio de belleza y bienestar
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('cliente.servicios') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                    Ver Servicios
                </a>
                <a href="{{ route('mis-turnos') }}" class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg border-2 border-purple-600 hover:bg-purple-50 transition">
                    Reservar Turno
                </a>
            </div>
        </div>

        <!-- Servicios Destacados -->
        <div class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900">Servicios Destacados</h2>
                <a href="{{ route('cliente.servicios') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                    Ver todos →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($serviciosDestacados as $servicio)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $servicio->nombre }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $servicio->descripcion }}</p>

                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $servicio->duracion }} min
                                </span>
                                <span class="text-2xl font-bold text-purple-600">${{ number_format($servicio->precio, 0) }}</span>
                            </div>

                            <a href="{{ route('mis-turnos') }}" class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                Reservar
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">No hay servicios disponibles</p>
                @endforelse
            </div>
        </div>

        <!-- Productos Destacados -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900">Productos Destacados</h2>
                <a href="{{ route('cliente.productos') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                    Ver todos →
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($productosDestacados as $producto)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <div class="aspect-square bg-gray-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 text-sm line-clamp-1">{{ $producto->nombre }}</h3>
                            @if($producto->categoria)
                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded mb-2">
                                    {{ $producto->categoria }}
                                </span>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-purple-600">${{ number_format($producto->precio, 0) }}</span>
                                <span class="text-xs text-gray-500">Stock: {{ $producto->stock }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-4 text-center text-gray-500">No hay productos disponibles</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
