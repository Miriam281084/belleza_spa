<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            {{-- Mensaje de éxito/error --}}
            <div x-data="{ mensaje: '', mostrar: false }"
                 @mostrar-mensaje.window="
                    mensaje = $event.detail.mensaje;
                    mostrar = true;
                    setTimeout(() => mostrar = false, 3000);
                 "
                 x-show="mostrar"
                 x-transition
                 class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                 style="display: none;">
                <span x-text="mensaje"></span>
            </div>

            {{-- Título + botón Nuevo Cliente (Admin & Recepcionista) --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Gestión de Clientes</h2>

                @role('Admin|Recepcionista')
                    <button
                        wire:click="abrirModal"
                        type="button"
                        class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-full transition"
                    >
                        Nuevo Cliente
                    </button>
                @endrole
            </div>

            {{-- Buscador --}}
            <div class="mb-4 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar por nombre, apellido, email o DNI..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-3">
                    <svg class="animate-spin h-5 w-5 text-pink-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            {{-- Tabla de clientes --}}
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gradient-to-r from-pink-50 to-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Nac.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>

                            {{-- Admin & Recepcionista pueden editar --}}
                            @role('Admin|Recepcionista')
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            @endrole
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clientes as $cliente)
                            <tr wire:key="cliente-{{ $cliente->id }}" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->apellido }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->dni }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->fecha_nacimiento ? \Carbon\Carbon::parse($cliente->fecha_nacimiento)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cliente->telefono ?? '-' }}
                                </td>

                                {{-- Acciones --}}
                                @role('Admin|Recepcionista')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            wire:click="editar({{ $cliente->id }})"
                                            wire:loading.attr="disabled"
                                            class="text-indigo-600 hover:text-indigo-900 transition disabled:opacity-50"
                                        >
                                            Editar
                                        </button>
                                    </td>
                                @endrole
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron clientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $clientes->links() }}
            </div>

            {{-- Modal crear / editar --}}
            @if($modalAbierto)
                <div class="fixed z-10 inset-0 overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                        {{-- Fondo --}}
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModal"></div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form wire:submit.prevent="guardar">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                                    {{-- Título --}}
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $editando ? 'Editar Cliente' : 'Nuevo Cliente' }}
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Nombre, apellido, DNI y fecha de nacimiento no se modifican una vez creado el cliente.
                                        </p>
                                    </div>

                                    {{-- Nombre --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                                        <input type="text"
                                               wire:model="nombre"
                                               @if($editando) disabled @endif
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                    </div>

                                    {{-- Apellido --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Apellido *</label>
                                        <input type="text"
                                               wire:model="apellido"
                                               @if($editando) disabled @endif
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                    </div>

                                    {{-- DNI --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">DNI *</label>
                                        <input type="text"
                                               wire:model="dni"
                                               @if($editando) disabled @endif
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                    </div>

                                    {{-- Fecha de nacimiento --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Fecha de nacimiento</label>
                                        <input type="date"
                                               wire:model="fecha_nacimiento"
                                               @if($editando) disabled @endif
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2 {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                    </div>

                                    {{-- Email --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700">Email *</label>
                                        <input type="email"
                                               wire:model="email"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2">
                                    </div>

                                    {{-- Teléfono --}}
                                    <div class="mb-2">
                                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                                        <input type="text"
                                               wire:model="telefono"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm px-3 py-2">
                                    </div>
                                </div>

                                {{-- Botones --}}
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                            class="w-full inline-flex justify-center rounded-full px-4 py-2 bg-pink-500 text-white hover:bg-pink-600 sm:ml-3 sm:w-auto sm:text-sm">
                                        {{ $editando ? 'Actualizar' : 'Guardar' }}
                                    </button>

                                    <button type="button"
                                            wire:click="cerrarModal"
                                            class="mt-3 w-full inline-flex justify-center rounded-full border px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
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
