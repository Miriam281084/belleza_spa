<div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4 md:p-6">

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 md:mb-6 gap-3">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Historial de Pagos</h2>
                <a href="{{ route('pagos.registrar') }}" class="w-full sm:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition text-sm md:text-base">
                    + Nuevo Pago
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="bg-green-50 p-3 md:p-4 rounded-lg">
                    <p class="text-xs md:text-sm text-green-600 font-semibold">Total Recaudado</p>
                    <p class="text-lg md:text-2xl font-bold text-green-700">${{ number_format($totalPagos, 0) }}</p>
                </div>
                @foreach($totalPorMetodo as $metodo)
                    <div class="bg-blue-50 p-3 md:p-4 rounded-lg">
                        <p class="text-xs md:text-sm text-blue-600 font-semibold capitalize">{{ $metodo->metodo_pago }}</p>
                        <p class="text-lg md:text-2xl font-bold text-blue-700">${{ number_format($metodo->total, 0) }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="sm:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente</label>
                    <input type="text" id="search" wire:model.live="search" placeholder="Nombre o email..." class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm">
                </div>
                <div>
                    <label for="fechaInicio" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" id="fechaInicio" wire:model.live="fechaInicio" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                </div>
                <div>
                    <label for="fechaFin" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" id="fechaFin" wire:model.live="fechaFin" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                </div>
                <div>
                    <label for="metodoFiltro" class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                    <select id="metodoFiltro" wire:model.live="metodoFiltro" class="w-full px-3 md:px-4 py-2 border border-gray-300 rounded-lg text-xs md:text-sm">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="mercadopago">Mercado Pago</option>
                    </select>
                </div>
            </div>

            <button wire:click="limpiarFiltros" class="text-xs md:text-sm text-blue-600 hover:text-blue-800 mb-4 md:mb-6">Limpiar filtros</button>

            <!-- Vista de tarjetas (móvil) -->
            <div class="block md:hidden space-y-4 mb-4">
                @forelse($pagos as $pago)
                    <div wire:key="pago-mobile-{{ $pago->id }}" class="border border-gray-200 rounded-lg p-4 bg-white">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 text-sm">#{{ $pago->id }}</span>
                                    <span class="text-xs text-gray-500">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700 font-medium">{{ $pago->cliente->nombre }}</p>
                            </div>
                            <span class="text-lg font-bold text-green-600">${{ number_format($pago->monto, 0) }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-2">
                            @if($pago->turno_id)
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Turno #{{ $pago->turno_id }}</span>
                            @elseif($pago->venta_id)
                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">Venta #{{ $pago->venta_id }}</span>
                            @endif
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded capitalize">{{ $pago->metodo_pago }}</span>
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded capitalize">{{ $pago->estado }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500 text-sm">
                        No se encontraron pagos.
                    </div>
                @endforelse
            </div>

            <!-- Tabla (desktop) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pagos as $pago)
                            <tr wire:key="pago-{{ $pago->id }}">
                                <td class="px-4 lg:px-6 py-3 text-sm">#{{ $pago->id }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">{{ $pago->fecha_pago->format('d/m/Y H:i') }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">{{ Str::limit($pago->cliente->nombre, 25) }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    @if($pago->turno_id)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Turno #{{ $pago->turno_id }}</span>
                                    @elseif($pago->venta_id)
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">Venta #{{ $pago->venta_id }}</span>
                                    @endif
                                </td>
                                <td class="px-4 lg:px-6 py-3 text-sm capitalize">{{ $pago->metodo_pago }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm font-semibold text-green-600">${{ number_format($pago->monto, 0) }}</td>
                                <td class="px-4 lg:px-6 py-3 text-sm">
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded capitalize">{{ $pago->estado }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No se encontraron pagos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $pagos->links() }}</div>

        </div>
    </div>
</div>
