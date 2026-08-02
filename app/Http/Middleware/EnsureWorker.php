<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorker
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // 1. Verificamos si el usuario está autenticado y es un empleado
        if (!$user || !$user->isEmployee()) {
            abort(403, 'Acceso denegado: No tienes un perfil de trabajador activo.');
        }

        // 2. Si se pasaron roles en la ruta (ej: 'worker:admin'), verificamos con hasWorkerRole()
        if (!empty($roles) && !$user->hasWorkerRole($roles)) {
            abort(403, 'Acceso denegado: Tu rol de empleado no tiene permiso para este recurso.');
        }

        return $next($request);
    }
}
