<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfCliente
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado y tiene el rol "Cliente"
        if (Auth::check() && Auth::user()->hasRole('Cliente')) {
            // Redirigir al Cliente a su página de turnos con un mensaje informativo
            return redirect()->route('mis-turnos')
                ->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        // Si no es Cliente, permitir acceso a la ruta
        return $next($request);
    }
}
