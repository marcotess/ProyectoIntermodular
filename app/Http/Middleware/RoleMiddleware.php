<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware para verificar uno o más roles de usuario.
 */
class RoleMiddleware
{
    /**
     * Comprueba que el usuario autenticado posee alguno de los roles recibidos.
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        abort_unless($user, 403);

        if ($roles === [] || $user->hasAnyRole($roles) || $this->tokenGrantsAnyRole($user, $roles)) {
            return $next($request);
        }

        abort(403);
    }

    private function tokenGrantsAnyRole($user, array $roles): bool
    {
        $token = $user->currentAccessToken();

        if (! $token) {
            return false;
        }

        foreach ($roles as $role) {
            if ($user->tokenCan('role:' . $role)) {
                return true;
            }
        }

        return false;
    }
}
