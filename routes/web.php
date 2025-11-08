<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ClientesIndex;
use App\Livewire\EmpleadosIndex;
use App\Livewire\ServiciosIndex;
use App\Livewire\TurnosCalendario;
use App\Livewire\ProductosIndex;
use App\Livewire\VentasCrear;
use App\Livewire\VentasHistorial;
use App\Livewire\PagosRegistrar;
use App\Livewire\PagosHistorial;
use App\Livewire\Dashboard;
use App\Livewire\MisTurnos;
use App\Livewire\ClienteInicio;
use App\Livewire\ClienteServicios;
use App\Livewire\ClienteProductos;
use App\Livewire\ClientePerfil;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\MercadoPagoController;

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
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('no.cliente');

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

    // Gestión de productos (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente'])->group(function () {
        Route::get('/productos', ProductosIndex::class)->name('productos.index');
    });

    // Gestión de ventas (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente'])->group(function () {
        Route::get('/ventas/crear', VentasCrear::class)->name('ventas.crear');
        Route::get('/ventas/historial', VentasHistorial::class)->name('ventas.historial');
    });

    // Gestión de pagos (requiere permiso y no ser Cliente)
    Route::middleware(['no.cliente', 'permission:ver pagos'])->group(function () {
        Route::get('/pagos/registrar', PagosRegistrar::class)->name('pagos.registrar');
        Route::get('/pagos/historial', PagosHistorial::class)->name('pagos.historial');
    });

    // Gestión de turnos (para empleados, requiere permiso)
    Route::middleware(['no.cliente', 'permission:ver turnos'])->group(function () {
        Route::get('/turnos', TurnosCalendario::class)->name('turnos.index');
        Route::get('/turnos/eventos', [TurnoController::class, 'obtenerEventos'])->name('turnos.eventos');
    });

    // ===== RUTAS PARA CLIENTES =====

    // Inicio / Catálogo (público para clientes)
    Route::get('/inicio', ClienteInicio::class)->name('cliente.inicio');

    // Servicios con filtros
    Route::get('/catalogo-servicios', ClienteServicios::class)->name('cliente.servicios');

    // Productos tipo e-commerce
    Route::get('/catalogo-productos', ClienteProductos::class)->name('cliente.productos');

    // Mis Turnos (para todos los usuarios autenticados, especialmente Clientes)
    Route::get('/mis-turnos', MisTurnos::class)->name('mis-turnos');

    // Carrito de compras y pagos
    Route::middleware(['permission:ver productos|ver ventas'])->group(function () {
        Route::get('/mi-carrito', VentasCrear::class)->name('mi-carrito');
        Route::get('/mis-compras', VentasHistorial::class)->name('mis-compras');
    });

    Route::middleware(['permission:ver pagos|crear pagos'])->group(function () {
        Route::get('/mis-pagos', PagosRegistrar::class)->name('mis-pagos');
        Route::get('/historial-pagos', PagosHistorial::class)->name('historial-pagos');
    });

    // Perfil del cliente
    Route::get('/mi-perfil', ClientePerfil::class)->name('cliente.perfil');
});

// Rutas de Mercado Pago (sin autenticación requerida para webhooks)
Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('/mercadopago/failure', [MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
Route::get('/mercadopago/pending', [MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook');
