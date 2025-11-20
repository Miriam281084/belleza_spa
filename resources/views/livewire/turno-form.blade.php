<!-- Modal del formulario de turno -->
<div>
    @if($modalAbierto)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cerrarModal"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="guardar">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">
                                {{ $turnoId ? 'Editar Turno' : 'Nuevo Turno' }}
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Fecha -->
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha *</label>
                                <input
                                    type="date"
                                    id="fecha"
                                    wire:model="fecha"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('fecha') border-red-500 @enderror"
                                >
                                @error('fecha')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Hora -->
                            <div>
                                <label for="hora" class="block text-sm font-medium text-gray-700">Hora *</label>
                                <input
                                    type="time"
                                    id="hora"
                                    wire:model="hora"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('hora') border-red-500 @enderror"
                                >
                                @error('hora')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Cliente -->
                            <div>
                                <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente *</label>
                                <select
                                    id="cliente_id"
                                    wire:model="cliente_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('cliente_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }}</option>
                                    @endforeach
                                </select>
                                @error('cliente_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Empleado -->
                            <div>
                                <label for="empleado_id" class="block text-sm font-medium text-gray-700">Empleado *</label>
                                <select
                                    id="empleado_id"
                                    wire:model="empleado_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('empleado_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un empleado</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}">{{ $empleado->nombre }} {{ $empleado->apellido }}</option>
                                    @endforeach
                                </select>
                                @error('empleado_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Servicio -->
                            <div>
                                <label for="servicio_id" class="block text-sm font-medium text-gray-700">Servicio *</label>
                                <select
                                    id="servicio_id"
                                    wire:model.live="servicio_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('servicio_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccione un servicio</option>
                                    @foreach($servicios as $servicio)
                                        <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('servicio_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700">Estado *</label>
                                <select
                                    id="estado"
                                    wire:model="estado"
                                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('estado') border-red-500 @enderror"
                                >
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="realizado">Realizado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                                @error('estado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Indicador de duración -->
                        @if($duracionServicio > 0)
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <p class="text-sm text-blue-800">
                                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <strong>Duración estimada:</strong> {{ $duracionServicio }} minutos
                                </p>
                            </div>
                        @endif

                        <!-- Observaciones -->
                        <div class="mt-4">
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea
                                id="observaciones"
                                wire:model="observaciones"
                                rows="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2 @error('observaciones') border-red-500 @enderror"
                            ></textarea>
                            @error('observaciones')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones del modal -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:justify-between">
                        <div class="flex flex-col-reverse sm:flex-row">
                            <button
                                type="button"
                                wire:click="cerrarModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="guardar"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="guardar">{{ $turnoId ? 'Actualizar' : 'Guardar' }}</span>
                                <span wire:loading wire:target="guardar">Guardando...</span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        </div>
    @endif
</div>
