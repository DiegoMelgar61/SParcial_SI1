<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
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

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            Log::warning('Usuario no encontrado', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'No existe un usuario con ese email.'
            ]);
        }

        if (!Hash::check($request->password, $usuario->contrasena)) {
            Log::warning('Contraseña incorrecta', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'La contraseña es incorrecta.'
            ]);
        }

        if ($usuario->estado !== 'activo') {
            Log::warning('Usuario inactivo', ['email' => $request->email]);
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

        // Intenta redirigir al dashboard
        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}