<div class="py-6 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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

            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-4 md:mb-6">Registrar Pago</h2>

            <form wire:submit.prevent="registrarPago">

                <!-- Tipo de pago -->
                <div class="mb-4 md:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Pago *</label>
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="tipo" value="turno" class="mr-2">
                            <span class="text-xs md:text-sm">Pago de Turno</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="tipo" value="venta" class="mr-2">
                            <span class="text-xs md:text-sm">Pago de Venta</span>
                        </label>
                    </div>
                </div>

                <!-- Selección de turno o venta -->
                <div class="mb-4 md:mb-6">
                    @if($tipo === 'turno')
                        <label for="id_referencia" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Turno *
                        </label>
                        <select
                            id="id_referencia"
                            wire:model.live="id_referencia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm @error('id_referencia') border-red-500 @enderror"
                        >
                            <option value="">Seleccione un turno</option>
                            @foreach($turnos as $turno)
                                <option value="{{ $turno->id }}">
                                    #{{ $turno->id }} - {{ $turno->cliente->nombre }} - {{ $turno->servicio->nombre }}
                                    ({{ $turno->fecha->format('d/m/Y H:i') }}) - Saldo: ${{ number_format($turno->saldoPendiente(), 0) }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <label for="id_referencia" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Venta *
                        </label>
                        <select
                            id="id_referencia"
                            wire:model.live="id_referencia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm @error('id_referencia') border-red-500 @enderror"
                        >
                            <option value="">Seleccione una venta</option>
                            @foreach($ventas as $venta)
                                <option value="{{ $venta->id }}">
                                    #{{ $venta->id }} - {{ $venta->cliente->nombre }}
                                    ({{ $venta->fecha->format('d/m/Y H:i') }}) - Saldo: ${{ number_format($venta->saldoPendiente(), 0) }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('id_referencia')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Información precargada -->
                @if($turnoSeleccionado || $ventaSeleccionada)
                    <div class="mb-4 md:mb-6 p-3 md:p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <h3 class="text-xs md:text-sm font-semibold text-blue-900 mb-2">Información del Pago</h3>
                        @if($turnoSeleccionado)
                            <div class="text-xs md:text-sm text-blue-800 space-y-1">
                                <p><strong>Cliente:</strong> {{ $turnoSeleccionado->cliente->nombre }}</p>
                                <p><strong>Servicio:</strong> {{ $turnoSeleccionado->servicio->nombre }}</p>
                                <p><strong>Fecha:</strong> {{ $turnoSeleccionado->fecha->format('d/m/Y H:i') }}</p>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p><strong>Precio del servicio:</strong> ${{ number_format($turnoSeleccionado->servicio->precio, 0) }}</p>
                                    <p><strong>Total pagado:</strong> ${{ number_format($turnoSeleccionado->totalPagado(), 0) }}</p>
                                    <p class="text-base md:text-lg font-bold mt-2">
                                        <strong>Saldo pendiente:</strong>
                                        <span class="text-red-700">${{ number_format($turnoSeleccionado->saldoPendiente(), 0) }}</span>
                                    </p>
                                </div>
                            </div>
                        @elseif($ventaSeleccionada)
                            <div class="text-xs md:text-sm text-blue-800 space-y-1">
                                <p><strong>Cliente:</strong> {{ $ventaSeleccionada->cliente->nombre }}</p>
                                <p><strong>Fecha:</strong> {{ $ventaSeleccionada->fecha->format('d/m/Y H:i') }}</p>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p><strong>Total de la venta:</strong> ${{ number_format($ventaSeleccionada->monto_total, 0) }}</p>
                                    <p><strong>Total pagado:</strong> ${{ number_format($ventaSeleccionada->totalPagado(), 0) }}</p>
                                    <p class="text-base md:text-lg font-bold mt-2">
                                        <strong>Saldo pendiente:</strong>
                                        <span class="text-red-700">${{ number_format($ventaSeleccionada->saldoPendiente(), 0) }}</span>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Cliente (solo si no hay referencia) -->
                @if(!$turnoSeleccionado && !$ventaSeleccionada)
                    <div class="mb-4 md:mb-6">
                        <label for="id_cliente" class="block text-sm font-medium text-gray-700 mb-2">Cliente *</label>
                        <select
                            id="id_cliente"
                            wire:model="id_cliente"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm @error('id_cliente') border-red-500 @enderror"
                        >
                            <option value="">Seleccione un cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_cliente')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <!-- Monto -->
                <div class="mb-4 md:mb-6">
                    <label for="monto" class="block text-sm font-medium text-gray-700 mb-2">Monto *</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-xs md:text-sm">$</span>
                        </div>
                        <input
                            type="number"
                            id="monto"
                            wire:model="monto"
                            min="0"
                            step="0.01"
                            class="block w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-xs md:text-sm pl-7 pr-3 py-2 @error('monto') border-red-500 @enderror"
                            placeholder="0.00"
                        >
                    </div>
                    @error('monto')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                    @if($turnoSeleccionado || $ventaSeleccionada)
                        <p class="text-xs text-gray-500 mt-1">
                            El monto se precarga con el saldo pendiente. Puede modificarlo para pagos parciales.
                        </p>
                    @else
                        <p class="text-xs text-gray-500 mt-1">Ingrese el monto del pago</p>
                    @endif
                </div>

                <!-- Método de pago -->
                <div class="mb-4 md:mb-6">
                    <label for="metodo" class="block text-sm font-medium text-gray-700 mb-2">Método de Pago *</label>
                    <select
                        id="metodo"
                        wire:model="metodo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm"
                    >
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="mercadopago">Mercado Pago</option>
                    </select>
                    @error('metodo')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Botón Mercado Pago (preparado para futuro) -->
                @if($metodo === 'mercadopago')
                    <div class="mb-4 md:mb-6 p-3 md:p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs md:text-sm text-yellow-800 mb-3">
                            <strong>Mercado Pago:</strong> Para usar este método de pago, debe configurar las credenciales en el archivo .env
                        </p>
                        <button
                            type="button"
                            wire:click="generarPreferenciaMercadoPago"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 md:px-4 rounded text-xs md:text-sm"
                        >
                            Generar Link de Pago
                        </button>
                        <p class="text-xs text-gray-600 mt-2">
                            Requiere: <code class="bg-gray-200 px-1 rounded">composer require mercadopago/dx-php</code>
                        </p>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="registrarPago"
                        class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 md:px-6 rounded-lg transition disabled:opacity-50 text-sm md:text-base"
                    >
                        <span wire:loading.remove wire:target="registrarPago">Registrar Pago</span>
                        <span wire:loading wire:target="registrarPago">Registrando...</span>
                    </button>

                    <a
                        href="{{ route('pagos.historial') }}"
                        class="w-full sm:w-auto text-center bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 md:px-6 rounded-lg transition text-sm md:text-base"
                    >
                        Ver Historial
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
