<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware que asegura que existe un usuario autenticado en sesión.
 */
class AuthMiddleware
{
    /**
     * manejo dela solicitud entrante
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        return $next($request);
    }
}
