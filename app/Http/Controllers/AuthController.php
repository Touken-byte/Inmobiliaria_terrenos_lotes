<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->rol === 'admin') {
                return redirect()->route('admin.panel');
            } elseif ($user->rol === 'comprador') {
                return redirect()->route('catalogo.terrenos');
            }
            return redirect()->route('vendedor.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar autenticación
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'activo' => 1 // Solo usuarios activos
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Actualizar último login
            $user->ultimo_login = now();
            $user->save();

            $redirectPath = '/vendedor/dashboard';
            if ($user->rol === 'admin') {
                $redirectPath = '/admin/panel';
            } elseif ($user->rol === 'comprador') {
                $redirectPath = '/catalogo';
            }

            return redirect()->intended($redirectPath)
                             ->with('success', '¡Bienvenido/a, ' . $user->nombre . '!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son correctas o el usuario está inactivo.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
