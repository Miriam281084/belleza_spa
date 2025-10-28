@extends('layouts.app')

@section('title', 'Bienvenido - Belleza Spa')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Bienvenido a Belleza Spa</h1>
                <p class="text-gray-600 mb-8">Sistema de Gestión Integral</p>

                <div class="flex justify-center gap-4">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700">
                        Ir al Dashboard
                    </a>
                    <a href="{{ route('clientes.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                        Gestionar Clientes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection