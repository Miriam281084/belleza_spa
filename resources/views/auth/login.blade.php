@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Belleza Spa')

@section('content')
<div class="w-full max-w-md">
    <!-- Logo y Título -->
    <div class="text-center mb-8">
        <div class="inline-block p-4 bg-white rounded-full shadow-lg mb-4">
            <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-bold text-white mb-2">Belleza Spa</h1>
        <p class="text-purple-100">Bienvenido de nuevo</p>
    </div>

    <!-- Formulario de Login -->
    <div class="glass-effect rounded-2xl shadow-2xl p-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>

        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Correo Electrónico
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 @error('email') border-red-500 @enderror"
                        placeholder="ejemplo@correo.com"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Contraseña
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200 @error('password') border-red-500 @enderror"
                        placeholder="••••••••"
                        required
                    >
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Recordarme
                    </label>
                </div>
                <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-[1.02] transition duration-200 shadow-lg"
            >
                Iniciar Sesión
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                ¿No tienes una cuenta?
                <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 font-semibold hover:underline">
                    Regístrate aquí
                </a>
            </p>
        </div>

        <!-- Divider -->
        <div class="mt-6 relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">Usuarios de prueba</span>
            </div>
        </div>

        <!-- Test Users Info -->
        <div class="mt-4 p-4 bg-purple-50 rounded-lg">
            <p class="text-xs text-gray-600 mb-2 font-semibold">Para probar el sistema:</p>
            <ul class="text-xs text-gray-600 space-y-1">
                <li><strong>Admin:</strong> admin@belleza.com / password</li>
                <li><strong>Recepcionista:</strong> recepcionista@belleza.com / password</li>
                <li><strong>Esteticista:</strong> esteticista@belleza.com / password</li>
                <li><strong>Cliente:</strong> cliente@belleza.com / password</li>
            </ul>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-center text-white text-sm mt-6">
        &copy; {{ date('Y') }} Belleza Spa. Todos los derechos reservados.
    </p>
</div>
@endsection