<div class="py-6 md:py-12" wire:poll.30s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6">Dashboard - Belleza & SPA Victoria</h1>

        <!-- Tarjetas de Métricas Principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-6">

            <!-- Turnos Hoy -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Turnos Hoy</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-600">{{ $turnosHoy }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-2 md:p-3">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4 flex flex-wrap gap-1 md:gap-2 text-xs">
                    <span class="px-2 py-0.5 md:py-1 bg-yellow-100 text-yellow-800 rounded">{{ $turnosPendientes }} pend.</span>
                    <span class="px-2 py-0.5 md:py-1 bg-green-100 text-green-800 rounded">{{ $turnosConfirmados }} conf.</span>
                    <span class="px-2 py-0.5 md:py-1 bg-blue-100 text-blue-800 rounded">{{ $turnosRealizados }} real.</span>
                    <span class="px-2 py-0.5 md:py-1 bg-red-100 text-red-800 rounded">{{ $turnosCancelados }} canc.</span>
                </div>
            </div>

            <!-- Ingresos Hoy -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Ingresos Hoy</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-600">${{ number_format($ingresosHoy, 0) }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-2 md:p-3">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4 text-xs text-gray-500">
                    Sem: ${{ number_format($ingresosSemana, 0) }} | Mes: ${{ number_format($ingresosMes, 0) }}
                </div>
            </div>

            <!-- Ventas Hoy -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Ventas Hoy</p>
                        <p class="text-2xl md:text-3xl font-bold text-purple-600">{{ $ventasHoy }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-2 md:p-3">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4 text-xs text-gray-500">
                    Monto: ${{ number_format($montoVentasHoy, 0) }}
                </div>
            </div>

            <!-- Clientes Nuevos -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Clientes Nuevos</p>
                        <p class="text-2xl md:text-3xl font-bold text-indigo-600">{{ $clientesNuevos }}</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-2 md:p-3">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3 md:mt-4 text-xs text-gray-500">
                    Esta semana
                </div>
            </div>

        </div>

        <!-- Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">

            <!-- Próximos Turnos -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Próximos Turnos de Hoy</h2>
                @forelse($proximosTurnos as $turno)
                    <div class="border-b border-gray-200 py-2 md:py-3 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 pr-2">
                                <p class="font-semibold text-gray-900 text-sm md:text-base">{{ Str::limit($turno->cliente->nombre, 25) }}</p>
                                <p class="text-xs md:text-sm text-gray-600">{{ Str::limit($turno->servicio->nombre, 30) }}</p>
                                <p class="text-xs text-gray-500">Con: {{ Str::limit($turno->empleado->nombre, 20) }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs md:text-sm font-bold text-gray-700">{{ substr($turno->hora, 0, 5) }}</p>
                                <span class="px-2 py-0.5 md:py-1 text-xs rounded capitalize font-medium
                                    @if($turno->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                    @elseif($turno->estado === 'confirmado') bg-green-100 text-green-800
                                    @elseif($turno->estado === 'realizado') bg-blue-100 text-blue-800
                                    @elseif($turno->estado === 'cancelado') bg-red-100 text-red-800
                                    @endif">
                                    {{ $turno->estado }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 text-sm">No hay turnos programados para hoy</p>
                @endforelse
            </div>

            <!-- Productos con Stock Bajo -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4">Stock Bajo</h2>
                @forelse($productosStockBajo as $producto)
                    <div class="border-b border-gray-200 py-2 md:py-3 last:border-b-0">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 pr-2">
                                <p class="font-semibold text-gray-900 text-xs md:text-sm">{{ Str::limit($producto->nombre, 18) }}</p>
                                <p class="text-xs text-gray-500">{{ $producto->categoria }}</p>
                            </div>
                            <span class="px-2 py-0.5 md:py-1 text-xs font-bold bg-red-100 text-red-800 rounded flex-shrink-0">
                                {{ $producto->stock }} unid.
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 text-xs md:text-sm">Todos los productos tienen stock suficiente</p>
                @endforelse
                @if($productosStockBajo->count() > 0)
                    <a href="{{ route('productos.index') }}" class="block mt-3 md:mt-4 text-center text-xs md:text-sm text-blue-600 hover:text-blue-800">
                        Ver todos los productos →
                    </a>
                @endif
            </div>

        </div>

        <!-- Indicador de actualización automática -->
        <div class="mt-4 text-center text-xs text-gray-500">
            Actualización automática cada 30 segundos
        </div>

    </div>
</div>
