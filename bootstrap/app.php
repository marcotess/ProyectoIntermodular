<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Permite que el frontend propio use sesión sobre /api, sin impedir Bearer tokens externos.
        $middleware->statefulApi();

        // Se añade al grupo web para que toda petición de navegador pase por la
        // auditoría de actividad sin tener que tocar cada ruta manualmente.
        $middleware->alias([
            'role' => App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->web(append: [
            App\Http\Middleware\LogUserActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
