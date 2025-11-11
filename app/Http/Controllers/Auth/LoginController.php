<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\AuditoriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        // Log para depuración
        Log::info('Intento de login', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::with('rol')->where('email', $request->email)->first();

        if (!$usuario) {
            Log::warning('Usuario no encontrado', ['email' => $request->email]);
            
            // Registrar intento fallido
            AuditoriaService::logLoginFallido($request->email, 'Usuario no encontrado');
            
            return back()->withErrors([
                'email' => 'No existe un usuario con ese email.'
            ]);
        }

        if (!Hash::check($request->password, $usuario->contrasena)) {
            Log::warning('Contraseña incorrecta', ['email' => $request->email]);
            
            // Registrar intento fallido
            AuditoriaService::logLoginFallido($request->email, 'Contraseña incorrecta');
            
            return back()->withErrors([
                'email' => 'La contraseña es incorrecta.'
            ]);
        }

        if ($usuario->estado !== 'activo') {
            Log::warning('Usuario inactivo', ['email' => $request->email]);
            
            // Registrar intento fallido
            AuditoriaService::logLoginFallido($request->email, 'Usuario inactivo');
            
            return back()->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacta al administrador.'
            ]);
        }

        Auth::login($usuario, $request->boolean('remember'));

        Log::info('Login exitoso', [
            'usuario_id' => $usuario->id,
            'email' => $usuario->email
        ]);

        $request->session()->regenerate();

        // Registrar login exitoso en auditoría
        AuditoriaService::logLogin($usuario);

        // Intenta redirigir al dashboard
        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        $usuario = Auth::user();
        
        // Registrar logout en auditoría antes de cerrar sesión
        if ($usuario) {
            try {
                AuditoriaService::logLogout($usuario);
            } catch (\Exception $e) {
                Log::error('Error al registrar logout en auditoría: ' . $e->getMessage());
            }
        }

        // Cerrar sesión
        Auth::logout();
        
        // Invalidar la sesión
        $request->session()->invalidate();
        
        // Regenerar el token CSRF después de invalidar la sesión
        $request->session()->regenerateToken();

        // Usar Inertia::location para hacer una redirección completa del navegador
        // Esto asegura que se obtenga un nuevo token CSRF
        return Inertia::location('/login');
    }
}