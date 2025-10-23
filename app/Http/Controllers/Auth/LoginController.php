<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        // Validar credenciales
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerar la sesión para prevenir ataques de fijación de sesión
            $request->session()->regenerate();

            // Obtener el usuario autenticado
            $user = Auth::user();

            // Redirigir según el rol del usuario
            try {
                if ($user->hasRole('Admin')) {
                    return redirect()->intended('/dashboard')->with('success', "Bienvenido/a {$user->name}");
                } elseif ($user->hasRole('Recepcionista')) {
                    return redirect()->intended('/dashboard')->with('success', "Bienvenido/a {$user->name}");
                } elseif ($user->hasRole('Esteticista')) {
                    return redirect()->intended('/turnos')->with('success', "Bienvenido/a {$user->name}");
                } else {
                    // Cliente u otro rol
                    return redirect()->intended('/mis-turnos')->with('success', "Bienvenido/a {$user->name}");
                }
            } catch (\Exception $e) {
                // Si hay error al verificar rol, logout y mostrar error
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Hubo un problema al verificar tus permisos. Por favor, contacta al administrador.',
                ]);
            }
        }

        // Credenciales incorrectas
        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // Cerrar la sesión del usuario
        Auth::logout();

        // Invalidar la sesión actual
        $request->session()->invalidate();

        // Regenerar el token CSRF para seguridad
        $request->session()->regenerateToken();

        // Redirigir al login con mensaje de éxito
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}