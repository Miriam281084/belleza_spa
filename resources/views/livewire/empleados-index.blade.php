<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <!-- Mensaje de éxito -->
            @if (session()->has('mensaje'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('mensaje') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Gestión de Empleados</h2>
                <button
                    wire:click="abrirModal"
                    type="button"
                    class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded transition"
                >
                    Nuevo Empleado
                </button>
            </div>

            <!-- Campo de búsqueda -->
            <div class="mb-4 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar por nombre, apellido, email o rol..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-3">
                    <svg class="animate-spin h-5 w-5 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Tabla de empleados -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gradient-to-r from-pink-50 to-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($empleados as $empleado)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->apellido }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->telefono ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php
                                        $rolConfig = [
                                            'admin' => ['color' => 'bg-purple-100 text-purple-800 border-purple-300', 'label' => 'Administrador'],
                                            'recepcionista' => ['color' => 'bg-blue-100 text-blue-800 border-blue-300', 'label' => 'Recepcionista'],
                                            'esteticista' => ['color' => 'bg-pink-100 text-pink-800 border-pink-300', 'label' => 'Esteticista']
                                        ];
                                        $config = $rolConfig[$empleado->rol] ?? ['color' => 'bg-gray-100 text-gray-800 border-gray-300', 'label' => ucfirst($empleado->rol)];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $config['color'] }}">
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button
                                        wire:click="editar({{ $empleado->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3 transition"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        wire:click="eliminar({{ $empleado->id }})"
                                        onclick="return confirm('¿Está seguro de eliminar este empleado?')"
                                        class="text-red-600 hover:text-red-900 transition"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron empleados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $empleados->links() }}
            </div>

            <!-- Modal -->
            @if($modalAbierto)
                <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cerrarModal"></div>

                        <!-- Center modal -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal panel -->
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form wire:submit.prevent="guardar">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="mb-4">
                                        <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                            {{ $editando ? 'Editar Empleado' : 'Nuevo Empleado' }}
                                        </h3>
                                    </div>

                                    <!-- Nombre -->
                                    <div class="mb-4">
                                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre *</label>
                                        <input
                                            type="text"
                                            id="nombre"
                                            wire:model="nombre"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('nombre') border-red-500 @enderror"
                                        >
                                        @error('nombre')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Apellido -->
                                    <div class="mb-4">
                                        <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido *</label>
                                        <input
                                            type="text"
                                            id="apellido"
                                            wire:model="apellido"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('apellido') border-red-500 @enderror"
                                        >
                                        @error('apellido')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input
                                            type="email"
                                            id="email"
                                            wire:model="email"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('email') border-red-500 @enderror"
                                        >
                                        @error('email')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="mb-4">
                                        <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                        <input
                                            type="text"
                                            id="telefono"
                                            wire:model="telefono"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('telefono') border-red-500 @enderror"
                                        >
                                        @error('telefono')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Rol -->
                                    <div class="mb-4">
                                        <label for="rol" class="block text-sm font-medium text-gray-700">Rol *</label>
                                        <select
                                            id="rol"
                                            wire:model="rol"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('rol') border-red-500 @enderror"
                                        >
                                            <option value="esteticista">Esteticista</option>
                                            <option value="recepcionista">Recepcionista</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                        @error('rol')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Botones del modal -->
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="guardar"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-pink-500 text-base font-medium text-white hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition"
                                    >
                                        <span wire:loading.remove wire:target="guardar">{{ $editando ? 'Actualizar' : 'Guardar' }}</span>
                                        <span wire:loading wire:target="guardar">Guardando...</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="cerrarModal"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition"
                                    >
                                        Cancelar
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
