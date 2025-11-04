<div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

            <div class="flex justify-between items-center mb-4 md:mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Historial de Ventas</h2>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
                <!-- Búsqueda por cliente -->
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente</label>
                    <input
                        type="text"
                        id="search"
                        wire:model.live="search"
                        placeholder="Nombre o email..."
                        class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                    >
                </div>

                <!-- Fecha inicio -->
                <div>
                    <label for="fechaInicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input
                        type="date"
                        id="fechaInicio"
                        wire:model.live="fechaInicio"
                        class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                    >
                </div>

                <!-- Fecha fin -->
                <div>
                    <label for="fechaFin" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input
                        type="date"
                        id="fechaFin"
                        wire:model.live="fechaFin"
                        class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                    >
                </div>
            </div>

            <!-- Botón limpiar filtros -->
            <div class="mb-4 md:mb-6">
                <button
                    wire:click="limpiarFiltros"
                    class="text-xs md:text-sm text-blue-600 hover:text-blue-800"
                >
                    Limpiar filtros
                </button>
            </div>

            <!-- Vista de tarjetas (móvil) -->
            <div class="block md:hidden space-y-4 mb-4">
                @forelse($ventas as $venta)
                    <div wire:key="venta-mobile-{{ $venta->id }}" class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900">#{{ $venta->id }}</span>
                                    <span class="text-xs text-gray-500">{{ $venta->fecha->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700 font-medium">{{ $venta->cliente->nombre }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-3 pb-3 border-b">
                            <span class="text-xs text-gray-600">{{ $venta->productos->count() }} producto(s)</span>
                            <span class="text-lg font-bold text-green-600">${{ number_format($venta->monto_total, 0) }}</span>
                        </div>

                        <button
                            wire:click="verDetalle({{ $venta->id }})"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm"
                        >
                            Ver Detalle
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">
                        No se encontraron ventas con los filtros aplicados.
                    </div>
                @endforelse
            </div>

            <!-- Tabla de ventas (desktop) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Productos</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Total</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ventas as $venta)
                            <tr wire:key="venta-{{ $venta->id }}">
                                <td class="px-4 lg:px-6 py-3 text-sm font-medium text-gray-900">
                                    #{{ $venta->id }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">
                                    {{ $venta->fecha->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">
                                    {{ Str::limit($venta->cliente->nombre, 30) }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm text-gray-900">
                                    {{ $venta->productos->count() }} producto(s)
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-green-600">
                                    ${{ number_format($venta->monto_total, 0) }}
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-medium">
                                    <button
                                        wire:click="verDetalle({{ $venta->id }})"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Ver Detalle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron ventas con los filtros aplicados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $ventas->links() }}
            </div>

            <!-- Modal de detalle de venta -->
            @if($mostrarDetalle && $ventaSeleccionada)
                <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <!-- Overlay -->
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="cerrarDetalle"></div>

                        <!-- Center modal -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                        <!-- Modal panel -->
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                                <div class="mb-4">
                                    <h3 class="text-base md:text-lg font-medium text-gray-900" id="modal-title">
                                        Detalle de Venta #{{ $ventaSeleccionada->id }}
                                    </h3>
                                </div>

                                <!-- Información de la venta -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6 bg-gray-50 p-3 md:p-4 rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600">Fecha:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $ventaSeleccionada->fecha->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Cliente:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $ventaSeleccionada->cliente->nombre }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $ventaSeleccionada->cliente->email }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Teléfono:</p>
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $ventaSeleccionada->cliente->telefono ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Productos de la venta -->
                                <div class="mb-4">
                                    <h4 class="text-sm md:text-base font-semibold text-gray-800 mb-3">Productos</h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                                    <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Precio Unit.</th>
                                                    <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cant.</th>
                                                    <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($ventaSeleccionada->productos as $producto)
                                                    <tr>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm text-gray-900">
                                                            {{ Str::limit($producto->nombre, 20) }}
                                                            <span class="block sm:hidden text-xs text-gray-500">${{ number_format($producto->pivot->precio_unitario, 0) }}</span>
                                                        </td>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                                            ${{ number_format($producto->pivot->precio_unitario, 0) }}
                                                        </td>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm text-gray-900">
                                                            {{ $producto->pivot->cantidad }}
                                                        </td>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-semibold text-gray-900">
                                                            ${{ number_format($producto->pivot->precio_unitario * $producto->pivot->cantidad, 0) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr>
                                                    <td colspan="3" class="px-2 md:px-4 py-2 text-right text-xs md:text-sm font-semibold text-gray-900">
                                                        Total:
                                                    </td>
                                                    <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-bold text-green-600">
                                                        ${{ number_format($ventaSeleccionada->monto_total, 0) }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Pagos de la venta -->
                                <div class="mb-4">
                                    <h4 class="text-sm md:text-base font-semibold text-gray-800 mb-3">Pagos Registrados</h4>
                                    @if($ventaSeleccionada->pagos->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                                        <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                                        <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                                        <th class="px-2 md:px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($ventaSeleccionada->pagos as $pago)
                                                        <tr>
                                                            <td class="px-2 md:px-4 py-2 text-xs md:text-sm text-gray-900">
                                                                {{ $pago->fecha_pago->format('d/m/Y H:i') }}
                                                            </td>
                                                            <td class="px-2 md:px-4 py-2 text-xs md:text-sm text-gray-900">
                                                                <span class="px-2 py-1 text-xs rounded-full
                                                                    @if($pago->metodo_pago === 'efectivo') bg-green-100 text-green-800
                                                                    @elseif($pago->metodo_pago === 'tarjeta') bg-blue-100 text-blue-800
                                                                    @elseif($pago->metodo_pago === 'transferencia') bg-purple-100 text-purple-800
                                                                    @else bg-yellow-100 text-yellow-800
                                                                    @endif">
                                                                    {{ ucfirst($pago->metodo_pago) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-semibold text-green-600">
                                                                ${{ number_format($pago->monto, 2) }}
                                                            </td>
                                                            <td class="px-2 md:px-4 py-2 text-xs md:text-sm">
                                                                <span class="px-2 py-1 text-xs rounded-full
                                                                    @if($pago->estado === 'completado') bg-green-100 text-green-800
                                                                    @else bg-orange-100 text-orange-800
                                                                    @endif">
                                                                    {{ ucfirst($pago->estado) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-gray-50">
                                                    <tr>
                                                        <td colspan="2" class="px-2 md:px-4 py-2 text-right text-xs md:text-sm font-semibold text-gray-900">
                                                            Total Pagado:
                                                        </td>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-bold text-green-600">
                                                            ${{ number_format($ventaSeleccionada->totalPagado(), 2) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="px-2 md:px-4 py-2 text-right text-xs md:text-sm font-semibold text-gray-900">
                                                            Saldo Pendiente:
                                                        </td>
                                                        <td class="px-2 md:px-4 py-2 text-xs md:text-sm font-bold
                                                            @if($ventaSeleccionada->saldoPendiente() > 0) text-red-600 @else text-green-600 @endif">
                                                            ${{ number_format($ventaSeleccionada->saldoPendiente(), 2) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 bg-gray-50 p-3 rounded">
                                            No hay pagos registrados para esta venta.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Botones del modal -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                                <button
                                    type="button"
                                    wire:click="cerrarDetalle"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
