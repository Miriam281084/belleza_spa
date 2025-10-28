<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'no.cliente' => \App\Http\Middleware\RedirectIfCliente::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Enviar recordatorios de turnos cada hora
        $schedule->command('recordatorios:turnos')->hourly();

        // Enviar felicitaciones de cumpleaños diariamente a las 8 AM
        $schedule->command('cumpleanos:enviar')->dailyAt('08:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejar error 419 (CSRF Token Mismatch)
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
            }
        });
    })->create();
