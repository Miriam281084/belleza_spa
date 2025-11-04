<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-4">
                    <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                    Pago Cancelado
                </h2>
                <p class="text-lg text-gray-600">
                    El pago no pudo ser procesado o fue cancelado.
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    No se ha realizado ningún cargo. Puede intentar nuevamente o contactar con nosotros si necesita ayuda.
                </p>

                <div class="mt-8 space-y-3">
                    <a href="{{ route('pagos.registrar') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Intentar Nuevamente
                    </a>
                    <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
