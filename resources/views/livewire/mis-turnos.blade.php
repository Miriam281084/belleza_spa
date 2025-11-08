<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-gradient-to-r from-purple-600 to-indigo-600">
                <h1 class="text-3xl font-bold text-white">Mis Turnos</h1>
                <p class="text-purple-100 mt-2">Gestiona tus citas y reservas en Belleza Spa Victoria</p>
            </div>
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Información de Usuario -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-800">{{ $cliente->nombre }} {{ $cliente->apellido }}</h2>
                            <p class="text-gray-600">{{ $cliente->email }}</p>
                            @if($cliente->telefono)
                                <p class="text-gray-500 text-sm">{{ $cliente->telefono }}</p>
                            @endif
                        </div>
                    </div>
                    <button
                        wire:click="abrirModal"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Agendar Turno
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Turnos Completados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Completados</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $estadisticas['completados'] }}</p>
                </div>
            </div>

            <!-- Turnos Pendientes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Pendientes</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $estadisticas['pendientes'] }}</p>
                </div>
            </div>

            <!-- Turnos Confirmados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Confirmados</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $estadisticas['confirmados'] }}</p>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-wrap gap-2">
                    <button
                        wire:click="cambiarFiltro('todos')"
                        class="px-4 py-2 rounded-md {{ $filtroEstado === 'todos' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    >
                        Todos
                    </button>
                    <button
                        wire:click="cambiarFiltro('pendiente')"
                        class="px-4 py-2 rounded-md {{ $filtroEstado === 'pendiente' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    >
                        Pendientes
                    </button>
                    <button
                        wire:click="cambiarFiltro('confirmado')"
                        class="px-4 py-2 rounded-md {{ $filtroEstado === 'confirmado' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    >
                        Confirmados
                    </button>
                    <button
                        wire:click="cambiarFiltro('realizado')"
                        class="px-4 py-2 rounded-md {{ $filtroEstado === 'realizado' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    >
                        Realizados
                    </button>
                    <button
                        wire:click="cambiarFiltro('cancelado')"
                        class="px-4 py-2 rounded-md {{ $filtroEstado === 'cancelado' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                    >
                        Cancelados
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Turnos -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Mis Turnos</h3>

                @if($turnos->count() > 0)
                    <div class="space-y-4">
                        @foreach($turnos as $turno)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $turno->servicio->nombre }}</h4>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                @if($turno->estado === 'pendiente') bg-yellow-100 text-yellow-800
                                                @elseif($turno->estado === 'confirmado') bg-green-100 text-green-800
                                                @elseif(in_array($turno->estado, ['realizado', 'completado'])) bg-blue-100 text-blue-800
                                                @elseif($turno->estado === 'cancelado') bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ ucfirst($turno->estado) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                                            <p class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}
                                            </p>
                                            <p class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Profesional: {{ $turno->empleado->nombre }} {{ $turno->empleado->apellido }}
                                            </p>
                                            <p class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Duración: {{ $turno->servicio->duracion }} minutos
                                            </p>
                                            <p class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Precio: ${{ number_format($turno->servicio->precio, 2) }}
                                            </p>
                                            @if($turno->observaciones)
                                                <p class="flex items-start">
                                                    <svg class="h-4 w-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                    </svg>
                                                    <span>{{ $turno->observaciones }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        @php
                                            // Usar los atributos raw para evitar problemas con el cast
                                            $fechaStr = $turno->fecha instanceof \Carbon\Carbon
                                                ? $turno->fecha->format('Y-m-d')
                                                : $turno->fecha;
                                            $fechaHora = \Carbon\Carbon::parse($fechaStr . ' ' . $turno->hora);
                                            $esFuturo = $fechaHora->isFuture();
                                        @endphp
                                        @if(in_array($turno->estado, ['pendiente', 'confirmado']) && $esFuturo)
                                            <button
                                                wire:click="confirmarCancelar({{ $turno->id }})"
                                                class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                                            >
                                                Cancelar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $turnos->links() }}
                    </div>
                @else
                    <!-- Estado vacío -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes turnos
                            @if($filtroEstado !== 'todos')
                                {{ $filtroEstado }}s
                            @endif
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Agenda tu próximo turno para disfrutar de nuestros servicios.
                        </p>
                        <div class="mt-6">
                            <button
                                wire:click="abrirModal"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Agendar Turno
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Crear Turno -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Agendar Nuevo Turno</h3>

                        <div class="space-y-4">
                            <!-- Servicio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Servicio *</label>
                                <select wire:model.defer="servicio_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Selecciona un servicio</option>
                                    @foreach($servicios as $servicio)
                                        <option value="{{ $servicio->id }}">
                                            {{ $servicio->nombre }} - ${{ number_format($servicio->precio, 2) }} ({{ $servicio->duracion }} min)
                                        </option>
                                    @endforeach
                                </select>
                                @error('servicio_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Empleado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profesional *</label>
                                <select wire:model.defer="empleado_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <option value="">Selecciona un profesional</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}">{{ $empleado->nombre }} {{ $empleado->apellido }}</option>
                                    @endforeach
                                </select>
                                @error('empleado_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                                <input type="date" wire:model="fecha" min="{{ now()->format('Y-m-d') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                @error('fecha') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Hora -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                                <input type="time" wire:model="hora"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                @error('hora') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Observaciones -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea wire:model="observaciones" rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                    placeholder="Algún comentario o preferencia especial..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="guardarTurno"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Agendar
                        </button>
                        <button
                            wire:click="cerrarModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmar Cancelación -->
    @if($showCancelModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModalCancelar"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Cancelar Turno
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        ¿Estás seguro que deseas cancelar este turno? Esta acción no se puede deshacer.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            wire:click="cancelarTurno"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Sí, cancelar
                        </button>
                        <button
                            wire:click="cerrarModalCancelar"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            No, mantener
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
