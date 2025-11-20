<div class="py-6 md:py-12" wire:poll.30s>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-4 md:mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Mi Agenda - {{ $fechaHoy }}
            </h1>
            @if($empleadoActual)
                <p class="text-gray-600 mt-1">Bienvenida, {{ $empleadoActual->nombre }} {{ $empleadoActual->apellido }}</p>
            @endif
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-4 md:mb-6">

            <!-- Total de Turnos -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="text-center">
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Total</p>
                    <p class="text-2xl md:text-3xl font-bold text-blue-600">{{ $estadisticas['total'] }}</p>
                </div>
            </div>

            <!-- Pendientes -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="text-center">
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Pendientes</p>
                    <p class="text-2xl md:text-3xl font-bold text-yellow-600">{{ $estadisticas['pendientes'] }}</p>
                </div>
            </div>

            <!-- Confirmados -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="text-center">
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Confirmados</p>
                    <p class="text-2xl md:text-3xl font-bold text-green-600">{{ $estadisticas['confirmados'] }}</p>
                </div>
            </div>

            <!-- Realizados -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="text-center">
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Realizados</p>
                    <p class="text-2xl md:text-3xl font-bold text-blue-500">{{ $estadisticas['realizados'] }}</p>
                </div>
            </div>

            <!-- Cancelados -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <div class="text-center">
                    <p class="text-xs md:text-sm font-medium text-gray-600 mb-1">Cancelados</p>
                    <p class="text-2xl md:text-3xl font-bold text-red-600">{{ $estadisticas['cancelados'] }}</p>
                </div>
            </div>

        </div>

        <!-- Lista de Turnos del Día -->
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Mis Citas de Hoy</h2>
                <a href="{{ route('turnos.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Ver calendario completo →
                </a>
            </div>

            @forelse($turnosHoy as $turno)
                <div class="border-b border-gray-200 py-3 md:py-4 last:border-b-0 hover:bg-gray-50 transition rounded px-2">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">

                        <!-- Hora -->
                        <div class="flex-shrink-0">
                            <div class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-bold text-sm md:text-base">{{ substr($turno->hora, 0, 5) }}</span>
                            </div>
                        </div>

                        <!-- Información del Cliente y Servicio -->
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 text-sm md:text-base">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }}
                            </p>
                            <p class="text-xs md:text-sm text-gray-600 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                {{ $turno->servicio->nombre }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $turno->cliente->telefono }}
                            </p>
                            @if($turno->observaciones)
                                <p class="text-xs text-gray-600 mt-2 bg-yellow-50 p-2 rounded border-l-2 border-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <strong>Observaciones:</strong> {{ $turno->observaciones }}
                                </p>
                            @endif
                        </div>

                        <!-- Estado -->
                        <div class="flex-shrink-0">
                            <span class="px-3 py-1 text-xs md:text-sm rounded-full capitalize font-semibold
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
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 text-sm md:text-base">No tienes citas programadas para hoy</p>
                    <p class="text-gray-400 text-xs mt-1">¡Disfruta tu día libre!</p>
                </div>
            @endforelse
        </div>

        <!-- Indicador de actualización automática -->
        <div class="mt-4 text-center text-xs text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Actualización automática cada 30 segundos
        </div>

    </div>
</div>
