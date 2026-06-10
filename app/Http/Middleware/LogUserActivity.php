<?php

namespace App\Http\Middleware;

use App\Support\UserActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * middleware que audita acciones web de usuarios autenticados
 *
 * 
 */
class LogUserActivity
{
    public function __construct(private readonly UserActivityLogger $activityLogger)
    {
    }
    /**
     * deja pasar la peticion y solo registra al final si la respuesta fue
     * correcta y el método HTTPindica
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        
        
        if (! in_array($request->getMethod(), config('activitylog.http_methods', []), true)) {
            return $response;
        }

        // login y logout ya se registran mediante eventos de autenticacion
        if (in_array($request->route()?->getName(), config('activitylog.ignored_routes', []), true)) {
            return $response;
        }

        // si falla, el error queda en el logn
        // normal de Laravel y no contarlo como una acción completada.
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $this->activityLogger->logWebAction($request, [
            'status_code' => $response->getStatusCode(),
        ]);

        return $response;
    }
}