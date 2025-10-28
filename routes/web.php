<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ClientesIndex;
use App\Livewire\EmpleadosIndex;
use App\Livewire\ServiciosIndex;
use App\Livewire\TurnosCalendario;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TurnoController;

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas con autenticación
Route::middleware(['auth'])->group(function () {

    // Dashboard (solo para empleados: Admin, Recepcionista, Esteticista)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('no.cliente');

    // Gestión de clientes (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente', 'permission:ver clientes'])->group(function () {
        Route::get('/clientes', ClientesIndex::class)->name('clientes.index');
    });

    // Gestión de empleados (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente', 'permission:ver empleados'])->group(function () {
        Route::get('/empleados', EmpleadosIndex::class)->name('empleados.index');
    });

    // Gestión de servicios (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente'])->group(function () {
        Route::get('/servicios', ServiciosIndex::class)->name('servicios.index');
    });

    // Gestión de turnos (para empleados, requiere permiso)
    Route::middleware(['no.cliente', 'permission:ver turnos'])->group(function () {
        Route::get('/turnos', TurnosCalendario::class)->name('turnos.index');
        Route::get('/turnos/eventos', [TurnoController::class, 'obtenerEventos'])->name('turnos.eventos');
    });

    // Mis Turnos (para todos los usuarios autenticados, especialmente Clientes)
    Route::get('/mis-turnos', function () {
        return view('turnos.mis-turnos');
    })->name('mis-turnos');
});
