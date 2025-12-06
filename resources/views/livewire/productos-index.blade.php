<div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

            <!-- Mensaje de éxito/error -->
            <div x-data="{ mensaje: '', mostrar: false, tipo: 'success' }"
                 @mostrar-mensaje.window="mensaje = $event.detail.mensaje; tipo = $event.detail.tipo || 'success'; mostrar = true; setTimeout(() => mostrar = false, 3000)"
                 x-show="mostrar"
                 x-transition
                 :class="tipo === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700'"
                 class="mb-4 border px-3 py-2 md:px-4 md:py-3 rounded relative text-sm md:text-base"
                 style="display: none;">
                <span x-text="mensaje"></span>
            </div>

            <!-- Header responsive -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 md:mb-6 gap-3">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Gestión de Productos</h2>

                @hasanyrole('Admin|Recepcionista')
                    <button
                        wire:click="abrirModal"
                        type="button"
                        class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 md:py-2 px-4 rounded-full transition text-sm md:text-base"
                    >
                        + Nuevo Producto
                    </button>
                @endhasanyrole
            </div>

            <!-- Campo de búsqueda -->
            <div class="mb-4 relative">
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Buscar productos..."
                    class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                >
                <div wire:loading wire:target="search" class="absolute right-3 top-2.5">
                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>

            <!-- Vista móvil -->
            <div class="block md:hidden space-y-4">
                @forelse($productos as $producto)
                    <div wire:key="producto-mobile-{{ $producto->id }}" class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $producto->nombre }}</h3>

                                <div class="mt-1 flex flex-wrap items-center gap-1">
                                    @if($producto->categoria)
                                        <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">
                                            {{ $producto->categoria }}
                                        </span>
                                    @endif

                                    @if($producto->estado === 'Inactivo')
                                        <span class="inline-block px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full border border-red-300">
                                            Inactivo
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full border border-green-300">
                                            Activo
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-lg font-bold text-blue-600">
                                ${{ number_format($producto->precio, 0) }}
                            </span>
                        </div>

                        @if($producto->descripcion)
                            <p class="text-sm text-gray-600 mb-3">
                                {{ Str::limit($producto->descripcion, 60) }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between mb-3 pb-3 border-b">
                            <span class="text-sm text-gray-600">Stock:</span>
                            @if($producto->stock < 5)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $producto->stock }} unidades
                                </span>
                            @else
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $producto->stock }} unidades
                                </span>
                            @endif
                        </div>

                        @hasanyrole('Admin|Recepcionista')
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Ajustar:</span>
                                <div class="flex items-center space-x-2">
                                    <button
                                        wire:click="restarStock({{ $producto->id }})"
                                        wire:loading.attr="disabled"
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded disabled:opacity-50"
                                    >
                                        -
                                    </button>
                                    <button
                                        wire:click="agregarStock({{ $producto->id }})"
                                        wire:loading.attr="disabled"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold px-3 py-1.5 rounded disabled:opacity-50"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <div>
                                <button
                                    wire:click="editar({{ $producto->id }})"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded text-sm"
                                >
                                    Editar
                                </button>
                            </div>
                        @endhasanyrole
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        No se encontraron productos.
                    </div>
                @endforelse
            </div>


            <!-- Tabla desktop -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Descripción</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>

                            @hasanyrole('Admin|Recepcionista')
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ajustar</th>
                                <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            @endhasanyrole
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($productos as $producto)
                            <tr wire:key="producto-{{ $producto->id }}">
                                <td class="px-4 lg:px-6 py-3 text-sm font-medium text-gray-900">
                                    {{ Str::limit($producto->nombre, 25) }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">
                                    {{ $producto->categoria ?? '-' }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $producto->descripcion ? Str::limit($producto->descripcion, 40) : '-' }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">
                                    ${{ number_format($producto->precio, 0) }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    @if($producto->stock < 5)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $producto->stock }}
                                        </span>
                                    @else
                                        <span class="text-gray-900">{{ $producto->stock }}</span>
                                    @endif
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    @if($producto->estado === 'Inactivo')
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-300">
                                            Inactivo
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-300">
                                            Activo
                                        </span>
                                    @endif
                                </td>

                                @hasanyrole('Admin|Recepcionista')
                                    <td class="px-4 lg:px-6 py-3">
                                        <div class="flex items-center space-x-1">
                                            <button
                                                wire:click="restarStock({{ $producto->id }})"
                                                class="bg-red-500 hover:bg-red-600 text-white font-bold px-2 py-1 rounded text-xs"
                                            >
                                                -
                                            </button>
                                            <button
                                                wire:click="agregarStock({{ $producto->id }})"
                                                class="bg-green-500 hover:bg-green-600 text-white font-bold px-2 py-1 rounded text-xs"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 lg:px-6 py-3 text-sm">
                                        <button
                                            wire:click="editar({{ $producto->id }})"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Editar
                                        </button>
                                    </td>
                                @endhasanyrole
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron productos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $productos->links() }}
            </div>

            <!-- Modal -->
            @if($modalAbierto)
               <!-- MODAL PARA CREAR / EDITAR PRODUCTOS -->
<div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <!-- FONDO OPACO -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             wire:click="cerrarModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <!-- CUADRO DEL MODAL -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden
                    shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-1">
                    {{ $editando ? 'Editar Producto' : 'Nuevo Producto' }}
                </h3>

                @if($editando)
                    <p class="text-xs text-gray-500 mb-4">
                        El nombre NO se modifica al editar.
                    </p>
                @else
                    <p class="text-xs text-gray-500 mb-4">
                        Complete los datos del nuevo producto.
                    </p>
                @endif

                <div class="space-y-3">

                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input
                            type="text"
                            wire:model="nombre"
                            @if($editando) disabled @endif
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                   @error('nombre') border-red-500 @enderror
                                   {{ $editando ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        >
                        @error('nombre')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <input
                            type="text"
                            wire:model="categoria"
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                   @error('categoria') border-red-500 @enderror"
                        >
                        @error('categoria')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea
                            wire:model="descripcion"
                            rows="3"
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                   @error('descripcion') border-red-500 @enderror"
                        ></textarea>
                        @error('descripcion')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Precio y Stock -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio *</label>
                            <input
                                type="number"
                                wire:model="precio"
                                step="0.01"
                                class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                       @error('precio') border-red-500 @enderror"
                            >
                            @error('precio')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock *</label>
                            <input
                                type="number"
                                wire:model="stock"
                                class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                       @error('stock') border-red-500 @enderror"
                            >
                            @error('stock')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado *</label>
                        <select
                            wire:model="estado"
                            class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm
                                   @error('estado') border-red-500 @enderror"
                        >
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        @error('estado')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                <button
                    type="button"
                    wire:click="guardar"
                    wire:loading.attr="disabled"
                    class="w-full inline-flex justify-center rounded-md border border-transparent
                           shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white
                           hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                    Guardar
                </button>

                <button
                    type="button"
                    wire:click="cerrarModal"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300
                           shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700
                           hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancelar
                </button>

            </div>
        </div>
    </div>
</div>

            @endif

        </div>
    </div>
</div>
