<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->rol);
        }

        return view('auth.login');
    }

    /**
     * Procesa el login.
     *
     * Flujo de acceso por rol:
     *  - Admin     → entra si activo = true (siempre verificado)
     *  - Vendedor  → entra con CUALQUIER estado_verificacion mientras activo = true
     *                El dashboard del vendedor controla qué puede hacer según su estado:
     *                  · pendiente  → solo puede subir/ver su CI
     *                  · rechazado  → puede ver el motivo y volver a subir CI
     *                  · verificado → acceso completo (crear terrenos, ver solicitudes, etc.)
     *  - Comprador → entra si activo = true
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'El formato del correo no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $email    = strtolower(trim($request->email));
        $password = $request->password;

        $usuario = Usuario::where('email', $email)->first();

        // Credenciales incorrectas
        if (!$usuario || !Hash::check($password, $usuario->password)) {
            return back()
                ->withInput(['email' => $request->email])
                ->withErrors(['email' => 'Correo o contraseña incorrectos.']);
        }

        // Cuenta desactivada por el admin
        if (!$usuario->activo) {
            return back()
                ->withInput(['email' => $request->email])
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        // ── NO bloqueamos por estado_verificacion aquí ──
        // El VendedorController::dashboard() ya muestra el estado correcto
        // y restringe las acciones según pendiente/rechazado/verificado.

        Auth::login($usuario, $request->boolean('remember'));

        // Actualizar último login sin disparar eventos del modelo
        $usuario->ultimo_login = now();
        $usuario->saveQuietly();

        $request->session()->regenerate();

        // Registrar auditoría de login
        Auditoria::registrar(
            'login',
            'usuario',
            $usuario->id,
            "Login exitoso — rol: {$usuario->rol}",
            $usuario->id
        );

        return $this->redirectByRole($usuario->rol);
    }

    /**
     * Cierra la sesión.
     */
    public function logout(Request $request)
    {
        // Registrar auditoría de logout antes de cerrar sesión
        if (Auth::check()) {
            Auditoria::registrar(
                'logout',
                'usuario',
                Auth::id(),
                'Sesión cerrada correctamente'
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Redirige según el rol del usuario autenticado.
     */
    private function redirectByRole(string $rol)
    {
        switch ($rol) {
            case 'admin':
                return redirect()->route('admin.panel');
            case 'vendedor':
                return redirect()->route('vendedor.dashboard');
            case 'comprador':
                return redirect()->route('catalogo.terrenos');
            default:
                return redirect('/login')->with('error', 'Rol no reconocido. Contacta al administrador.');
        }
    }
}