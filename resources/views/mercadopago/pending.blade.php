<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                    Pago Pendiente
                </h2>
                <p class="text-lg text-gray-600">
                    Tu pago está siendo procesado.
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    Recibirás una confirmación cuando se complete el proceso. Esto puede tardar unos minutos.
                </p>

                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Te notificaremos por email cuando tu pago sea confirmado.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Volver al Dashboard
                    </a>
                    <a href="{{ route('pagos.historial') }}" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Ver Historial de Pagos
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
