<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <!-- Mensaje de éxito -->
            <div x-data="{ mensaje: '', mostrar: false }"
                 @mostrar-mensaje.window="mensaje = $event.detail.mensaje; mostrar = true; setTimeout(() => mostrar = false, 3000)"
                 x-show="mostrar"
                 x-transition
                 class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                 style="display: none;">
                <span x-text="mensaje"></span>
            </div>

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Gestión de Servicios</h2>

                {{-- Solo si NO está en solo lectura (admin u otro rol con permisos) --}}
                @unless($soloLectura)
                    <button
                        wire:click="abrirModal"
                        type="button"
                        class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-full transition"
                    >
                        Nuevo Servicio
                    </button>
                @endunless
            </div>

            <!-- Campo de búsqueda -->
            <div class="mb-4 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar por nombre o descripción..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-3">
                    <svg class="animate-spin h-5 w-5 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Tabla de servicios -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gradient-to-r from-pink-50 to-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración (min)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            @unless($soloLectura)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($servicios as $servicio)
                            <tr wire:key="servicio-{{ $servicio->id }}" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $servicio->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $servicio->descripcion ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $servicio->duracion }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    $ {{ number_format($servicio->precio, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($servicio->estado === 'Inactivo')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-300">
                                            Inactivo
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-300">
                                            Activo
                                        </span>
                                    @endif
                                </td>

                                @unless($soloLectura)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            wire:click="editar({{ $servicio->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="editar"
                                            class="text-indigo-600 hover:text-indigo-900 disabled:opacity-50"
                                        >
                                            Editar
                                        </button>
                                    </td>
                                @endunless
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $soloLectura ? '5' : '6' }}" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron servicios.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $servicios->links() }}
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
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form wire:submit.prevent="guardar">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                            {{ $editando ? 'Editar Servicio' : 'Nuevo Servicio' }}
                                        </h3>
                                        @if($editando)
                                            <p class="text-xs text-gray-500 mt-1">
                                                El nombre del servicio no se modifica, solo descripción, duración, precio y estado.
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Nombre del servicio -->
                                    <div class="mb-4">
                                        <label for="nombre_servicio" class="block text-sm font-medium text-gray-700">
                                            Nombre del servicio *
                                        </label>
                                        <input
                                            type="text"
                                            id="nombre_servicio"
                                            wire:model="nombre_servicio"
                                            @if($editando) disabled @endif
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('nombre_servicio') border-red-500 @enderror {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                        >
                                        @error('nombre_servicio')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Descripción -->
                                    <div class="mb-4">
                                        <label for="descripcion" class="block text-sm font-medium text-gray-700">
                                            Descripción
                                        </label>
                                        <textarea
                                            id="descripcion"
                                            wire:model="descripcion"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('descripcion') border-red-500 @enderror"
                                        ></textarea>
                                        @error('descripcion')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Duración -->
                                    <div class="mb-4">
                                        <label for="duracion" class="block text-sm font-medium text-gray-700">
                                            Duración (minutos) *
                                        </label>
                                        <input
                                            type="number"
                                            id="duracion"
                                            wire:model="duracion"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('duracion') border-red-500 @enderror"
                                            min="15"
                                            step="5"
                                        >
                                        @error('duracion')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Precio -->
                                    <div class="mb-4">
                                        <label for="precio" class="block text-sm font-medium text-gray-700">
                                            Precio *
                                        </label>
                                        <input
                                            type="number"
                                            id="precio"
                                            wire:model="precio"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('precio') border-red-500 @enderror"
                                            min="0"
                                            step="0.01"
                                        >
                                        @error('precio')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Estado -->
                                    <div class="mb-2">
                                        <label for="estado" class="block text-sm font-medium text-gray-700">
                                            Estado *
                                        </label>
                                        <select
                                            id="estado"
                                            wire:model="estado"
                                            class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm px-3 py-2 @error('estado') border-red-500 @enderror"
                                        >
                                            <option value="Activo">Activo</option>
                                            <option value="Inactivo">Inactivo</option>
                                        </select>
                                        @error('estado')
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
                                        class="w-full inline-flex justify-center rounded-full border border-transparent shadow-sm px-4 py-2 bg-pink-500 text-base font-medium text-white hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 transition"
                                    >
                                        <span wire:loading.remove wire:target="guardar">{{ $editando ? 'Actualizar' : 'Guardar' }}</span>
                                        <span wire:loading wire:target="guardar">Guardando...</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="cerrarModal"
                                        class="mt-3 w-full inline-flex justify-center rounded-full border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition"
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

