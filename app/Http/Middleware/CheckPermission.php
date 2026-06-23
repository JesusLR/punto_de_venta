<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check() || !Auth::user()->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'lSuccess' => false,
                    'cMensaje' => 'No tiene los permisos necesarios para realizar esta acción.'
                ], 403);
            }
            
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
