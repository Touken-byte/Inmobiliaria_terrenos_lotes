<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Redirige según el rol real del usuario si intenta acceder
     * a una ruta que no le corresponde.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Si el rol del usuario está dentro de los roles permitidos, continuar
        if (in_array($user->rol, $roles)) {
            return $next($request);
        }

        // Redirigir a la zona correcta según el rol real del usuario
        return match($user->rol) {
            'admin'     => redirect('/admin/panel')
                            ->with('error', 'No tienes permisos para acceder a esa área.'),
            'vendedor'  => redirect('/vendedor/dashboard')
                            ->with('error', 'No tienes permisos para acceder a esa área.'),
            'comprador' => redirect('/catalogo')
                            ->with('error', 'No tienes permisos para acceder a esa área.'),
            default     => redirect('/login')
                            ->with('error', 'Tu cuenta no tiene un rol válido. Contacta al administrador.'),
        };
    }
}