<?php

namespace App\Http\Middleware;

use App\Reparacion;
use Closure;

class EnsureRepairAccess
{
    public function handle($request, Closure $next)
    {
        $reparacion = $request->route('reparacion');
        if ($reparacion) {
            if (!$reparacion instanceof Reparacion) $reparacion = Reparacion::findOrFail($reparacion);
            abort_unless($request->user() && $reparacion->accesiblePor($request->user()), 404);
        }
        return $next($request);
    }
}
