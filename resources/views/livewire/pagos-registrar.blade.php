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

                <!-- Filtros de tipo de pago -->
                <div class="mb-4 md:mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por tipo *</label>
                    <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" wire:model.live="filtroTurnos" class="mr-2 h-4 w-4 text-blue-600 rounded">
                            <span class="text-xs md:text-sm">Pagos de Turnos</span>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="checkbox" wire:model.live="filtroVentas" class="mr-2 h-4 w-4 text-blue-600 rounded">
                            <span class="text-xs md:text-sm">Pagos de Ventas</span>
                        </label>
                    </div>
                    @if(!$filtroTurnos && !$filtroVentas)
                        <p class="text-xs text-gray-500 mt-1">Selecciona al menos un tipo para continuar</p>
                    @endif
                </div>

                <!-- Selección de turno y/o venta -->
                @if($filtroTurnos || $filtroVentas)
                <div class="mb-4 md:mb-6">
                    <label for="id_referencia" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar
                        @if($filtroTurnos && $filtroVentas)
                            Turno o Venta *
                        @elseif($filtroTurnos)
                            Turno *
                        @else
                            Venta *
                        @endif
                    </label>
                    <select
                        id="id_referencia"
                        wire:model.live="id_referencia"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-xs md:text-sm @error('id_referencia') border-red-500 @enderror"
                    >
                        <option value="">-- Seleccione --</option>

                        @if($filtroTurnos)
                            <optgroup label="Turnos Pendientes">
                                @forelse($turnos as $turno)
                                    <option value="turno-{{ $turno->id }}">
                                        Turno #{{ $turno->id }} - {{ $turno->cliente->nombre }} - {{ $turno->servicio->nombre }}
                                        ({{ $turno->fecha->format('d/m/Y H:i') }}) - Saldo: ${{ number_format($turno->saldoPendiente(), 2) }}
                                    </option>
                                @empty
                                    <option disabled>No hay turnos con saldo pendiente</option>
                                @endforelse
                            </optgroup>
                        @endif

                        @if($filtroVentas)
                            <optgroup label="Ventas Pendientes">
                                @forelse($ventas as $venta)
                                    <option value="venta-{{ $venta->id }}">
                                        Venta #{{ $venta->id }} - {{ $venta->cliente->nombre }}
                                        ({{ $venta->fecha->format('d/m/Y H:i') }}) - Saldo: ${{ number_format($venta->saldoPendiente(), 2) }}
                                    </option>
                                @empty
                                    <option disabled>No hay ventas con saldo pendiente</option>
                                @endforelse
                            </optgroup>
                        @endif
                    </select>
                    @error('id_referencia')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                @endif

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
                                    <p><strong>Precio del servicio:</strong> ${{ number_format($turnoSeleccionado->servicio->precio, 2) }}</p>
                                    <p><strong>Total pagado:</strong> ${{ number_format($turnoSeleccionado->totalPagado(), 2) }}</p>
                                    <p class="text-base md:text-lg font-bold mt-2">
                                        <strong>Saldo pendiente:</strong>
                                        <span class="text-red-700">${{ number_format($turnoSeleccionado->saldoPendiente(), 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        @elseif($ventaSeleccionada)
                            <div class="text-xs md:text-sm text-blue-800 space-y-1">
                                <p><strong>Cliente:</strong> {{ $ventaSeleccionada->cliente->nombre }}</p>
                                <p><strong>Fecha:</strong> {{ $ventaSeleccionada->fecha->format('d/m/Y H:i') }}</p>
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p><strong>Total de la venta:</strong> ${{ number_format($ventaSeleccionada->monto_total, 2) }}</p>
                                    <p><strong>Total pagado:</strong> ${{ number_format($ventaSeleccionada->totalPagado(), 2) }}</p>
                                    <p class="text-base md:text-lg font-bold mt-2">
                                        <strong>Saldo pendiente:</strong>
                                        <span class="text-red-700">${{ number_format($ventaSeleccionada->saldoPendiente(), 2) }}</span>
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
                        wire:model.live="metodo"
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

                <!-- Botón Mercado Pago -->
                @if($metodo === 'mercadopago')
                    <div class="mb-4 md:mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                @if($mercadopago_url)
                                    <!-- Link y QR generados exitosamente -->
                                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-sm text-green-800 font-semibold mb-2">
                                            ✅ Link de pago generado exitosamente
                                        </p>
                                        <p class="text-xs text-green-700 mb-3">
                                            Elige una opción para pagar:
                                        </p>

                                        <!-- Opciones de pago -->
                                        <div class="flex flex-col md:flex-row gap-4">
                                            <!-- Opción 1: Botón Link -->
                                            <div class="flex-1">
                                                <p class="text-xs font-semibold text-gray-700 mb-2">💳 Pagar desde navegador</p>
                                                <a
                                                    href="{{ $mercadopago_url }}"
                                                    target="_blank"
                                                    class="inline-flex items-center justify-center w-full px-4 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105"
                                                >
                                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                    Abrir Mercado Pago
                                                </a>
                                                <p class="text-xs text-gray-600 mt-1 text-center">
                                                    Se abre en nueva pestaña
                                                </p>
                                            </div>

                                            <!-- Opción 2: Código QR -->
                                            <div class="flex-1 text-center">
                                                <p class="text-xs font-semibold text-gray-700 mb-2">📱 Escanear con celular</p>
                                                <div class="inline-block p-2 bg-white rounded-lg shadow-md">
                                                    <img src="{{ $mercadopago_qr }}" alt="Código QR Mercado Pago" class="w-40 h-40 md:w-48 md:h-48">
                                                </div>
                                                <p class="text-xs text-gray-600 mt-2">
                                                    Escanea con la app de Mercado Pago
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Botón para generar link -->
                                    @if(!$id_referencia || !$id_cliente || !$monto)
                                        <p class="text-sm text-amber-700 mb-3 font-medium">
                                            ⚠️ Complete todos los campos requeridos arriba para habilitar el pago con Mercado Pago.
                                        </p>
                                    @else
                                        <p class="text-sm text-blue-700 mb-3">
                                            Haz clic en "Generar Link de Pago" para crear el link de Mercado Pago.
                                        </p>
                                    @endif
                                    <button
                                        type="button"
                                        wire:click="$call('generarPreferenciaMercadoPago')"
                                        onclick="console.log('Botón clickeado'); this.disabled=true; this.innerText='Generando...';"
                                        wire:loading.attr="disabled"
                                        wire:target="generarPreferenciaMercadoPago"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="btnGenerarMP"
                                    >
                                        <svg wire:loading.remove wire:target="generarPreferenciaMercadoPago" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        <svg wire:loading wire:target="generarPreferenciaMercadoPago" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="generarPreferenciaMercadoPago">🔗 Generar Link de Pago</span>
                                        <span wire:loading wire:target="generarPreferenciaMercadoPago">Generando link...</span>
                                    </button>
                                    <p class="text-xs text-gray-500 mt-2">Si no funciona, presiona Ctrl+F5 para refrescar</p>
                                    <p class="text-xs text-gray-600 mt-2">
                                        Aceptamos tarjetas de crédito, débito y otros medios de pago
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botones de acción -->
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                    @if($metodo !== 'mercadopago')
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="registrarPago"
                            class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 md:px-6 rounded-lg transition disabled:opacity-50 text-sm md:text-base"
                        >
                            <span wire:loading.remove wire:target="registrarPago">Registrar Pago</span>
                            <span wire:loading wire:target="registrarPago">Registrando...</span>
                        </button>
                    @else
                        <div class="w-full sm:w-auto bg-gray-300 text-gray-500 font-bold py-2 px-4 md:px-6 rounded-lg text-sm md:text-base text-center cursor-not-allowed">
                            <span>⚠️ Usa el botón azul arriba para Mercado Pago</span>
                        </div>
                    @endif

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
