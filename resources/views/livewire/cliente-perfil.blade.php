<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Mi Perfil</h1>
            <p class="text-gray-600">Gestiona tu información personal y revisa tu historial</p>
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $estadisticas['total_turnos'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Turnos Totales</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $estadisticas['total_compras'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Compras</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-green-600">${{ number_format($estadisticas['total_gastado'], 0) }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Gastado</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <div class="text-3xl font-bold text-orange-600">${{ number_format($estadisticas['saldo_pendiente'], 0) }}</div>
                <div class="text-sm text-gray-600 mt-1">Saldo Pendiente</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button wire:click="cambiarTab('datos')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $tabActiva === 'datos' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Datos Personales
                    </button>
                    <button wire:click="cambiarTab('turnos')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $tabActiva === 'turnos' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Mis Turnos
                    </button>
                    <button wire:click="cambiarTab('compras')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $tabActiva === 'compras' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Mis Compras
                    </button>
                    <button wire:click="cambiarTab('pagos')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $tabActiva === 'pagos' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Mis Pagos
                    </button>
                </nav>
            </div>

            <div class="p-6">
                @if($tabActiva === 'datos')
                    <!-- Datos Personales -->
                    <div class="max-w-2xl">
                        @if(!$mostrarFormulario)
                            <div class="space-y-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-grow">
                                        <h3 class="text-lg font-semibold text-gray-900">Información Personal</h3>
                                        <div class="mt-4 space-y-3">
                                            <div>
                                                <span class="text-sm text-gray-600">Nombre completo:</span>
                                                <p class="font-medium">{{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-600">Email:</span>
                                                <p class="font-medium">{{ $cliente->email }}</p>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-600">DNI:</span>
                                                <p class="font-medium">{{ $cliente->dni ?: 'No especificado' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-600">Teléfono:</span>
                                                <p class="font-medium">{{ $cliente->telefono ?: 'No especificado' }}</p>
                                            </div>
                                            <div>
                                                <span class="text-sm text-gray-600">Fecha de nacimiento:</span>
                                                <p class="font-medium">{{ $cliente->fecha_nacimiento ? \Carbon\Carbon::parse($cliente->fecha_nacimiento)->format('d/m/Y') : 'No especificada' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="abrirFormulario" class="ml-4 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                        Editar
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Formulario de edición -->
                            <form wire:submit.prevent="guardarDatos" class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                                        <input type="text" wire:model="nombre" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        @error('nombre') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
                                        <input type="text" wire:model="apellido" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        @error('apellido') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                    <input type="email" wire:model="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                                        <input type="text" wire:model="dni" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        @error('dni') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                        <input type="text" wire:model="telefono" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        @error('telefono') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                                    <input type="date" wire:model="fecha_nacimiento" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    @error('fecha_nacimiento') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                        Guardar
                                    </button>
                                    <button type="button" wire:click="cerrarFormulario" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>

                @elseif($tabActiva === 'turnos')
                    <!-- Historial de Turnos -->
                    <div class="space-y-4">
                        @forelse($turnos as $turno)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-lg text-gray-900">{{ $turno->servicio->nombre }}</h4>
                                        <div class="mt-2 space-y-1 text-sm text-gray-600">
                                            <p>📅 {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}</p>
                                            <p>👤 Profesional: {{ $turno->empleado->nombre }}</p>
                                            <p>💰 Precio: ${{ number_format($turno->servicio->precio, 0) }}</p>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $turno->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $turno->estado === 'confirmado' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $turno->estado === 'realizado' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $turno->estado === 'cancelado' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($turno->estado) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No tienes turnos registrados</p>
                        @endforelse

                        {{ $turnos->links() }}
                    </div>

                @elseif($tabActiva === 'compras')
                    <!-- Historial de Compras -->
                    <div class="space-y-4">
                        @forelse($compras as $compra)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">Compra #{{ $compra->id }}</h4>
                                        <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-purple-600">${{ number_format($compra->monto_total, 0) }}</span>
                                </div>

                                <div class="space-y-2">
                                    @foreach($compra->productos as $producto)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $producto->nombre }} (x{{ $producto->pivot->cantidad }})</span>
                                            <span>${{ number_format($producto->pivot->precio_unitario * $producto->pivot->cantidad, 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 pt-3 border-t">
                                    <div class="flex justify-between text-sm">
                                        <span>Pagado:</span>
                                        <span class="text-green-600 font-semibold">${{ number_format($compra->totalPagado(), 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span>Pendiente:</span>
                                        <span class="{{ $compra->saldoPendiente() > 0 ? 'text-orange-600' : 'text-green-600' }} font-semibold">
                                            ${{ number_format($compra->saldoPendiente(), 0) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No tienes compras registradas</p>
                        @endforelse

                        {{ $compras->links() }}
                    </div>

                @elseif($tabActiva === 'pagos')
                    <!-- Historial de Pagos -->
                    <div class="space-y-3">
                        @forelse($pagos as $pago)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900">
                                                @if($pago->turno)
                                                    Pago de Turno #{{ $pago->turno->id }}
                                                @elseif($pago->venta)
                                                    Pago de Compra #{{ $pago->venta->id }}
                                                @else
                                                    Pago #{{ $pago->id }}
                                                @endif
                                            </h4>
                                            <span class="px-2 py-1 text-xs font-semibold rounded
                                                {{ $pago->estado === 'completado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($pago->estado) }}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-600">
                                            <p>📅 {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</p>
                                            <p>💳 Método: {{ ucfirst($pago->metodo_pago) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-600">${{ number_format($pago->monto, 0) }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-8">No tienes pagos registrados</p>
                        @endforelse

                        {{ $pagos->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
