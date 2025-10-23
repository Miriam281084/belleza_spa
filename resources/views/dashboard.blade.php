@extends('layouts.app')

@section('title', 'Dashboard - Belleza Spa')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Dashboard</h2>
            <p class="text-gray-600 mb-6">Bienvenido al sistema de gestión del Spa.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Clientes -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 text-sm font-semibold uppercase">Clientes</p>
                            <p class="text-3xl font-bold text-blue-700 mt-2">
                                {{ \App\Models\Cliente::count() }}
                            </p>
                        </div>
                        <div class="text-blue-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Ver todos →
                        </a>
                    </div>
                </div>

                <!-- Card Placeholder 2 -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 text-sm font-semibold uppercase">Servicios</p>
                            <p class="text-3xl font-bold text-green-700 mt-2">--</p>
                        </div>
                        <div class="text-green-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-gray-400 text-sm">Próximamente</span>
                    </div>
                </div>

                <!-- Card Placeholder 3 -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-600 text-sm font-semibold uppercase">Turnos</p>
                            <p class="text-3xl font-bold text-purple-700 mt-2">--</p>
                        </div>
                        <div class="text-purple-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-gray-400 text-sm">Próximamente</span>
                    </div>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('clientes.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Gestionar Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection