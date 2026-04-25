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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Validar si el rol del usuario está dentro de los roles permitidos
        if (!in_array($user->rol, $roles)) {
            // Si intenta acceder a un lugar indebido, redirigir a su lugar correspondiente
            if ($user->rol === 'admin') {
                return redirect('/admin/panel')->with('error', 'No tienes permisos para acceder a esa área.');
            }
            return redirect('/vendedor/dashboard')->with('error', 'No tienes permisos para acceder a esa área.');
        }

        return $next($request);
    }
}
