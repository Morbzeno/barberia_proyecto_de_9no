<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Permite el paso solo a usuarios autenticados cuyo registro de
     * empleado tenga admin_type = 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para continuar.');
        }

        $employee = $user->employee;

        if (!$employee || $employee->admin_type !== 'admin') {
            abort(403, 'No tienes permisos de administrador para acceder a esta sección.');
        }

        return $next($request);
    }
}
