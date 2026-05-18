<?php

namespace App\Providers;

use App\Observers\ModelActivityObserver;
use App\Support\UserActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Se registran observers genéricos para los modelos definidos en la
        // configuración, centralizando la auditoría sin ensuciar los modelos.
        foreach (config('activitylog.observed_models', []) as $modelClass) {
            $modelClass::observe(ModelActivityObserver::class);
        }

        // Evento nativo de Laravel que confirma una autenticación correcta.
        Event::listen(Login::class, function (Login $event): void {
            app(UserActivityLogger::class)->logAuth('auth.login', [
                'auth_guard' => $event->guard,
                'authenticated_user_id' => $event->user->getAuthIdentifier(),
                'authenticated_user_email' => $event->user->email,
            ]);
        });

        // evento nativo de Laravel al cerrar sesion
        Event::listen(Logout::class, function (Logout $event): void {
            app(UserActivityLogger::class)->logAuth('auth.logout', [
                'auth_guard' => $event->guard,
                'authenticated_user_id' => $event->user?->getAuthIdentifier(),
                'authenticated_user_email' => $event->user?->email,
            ]);
        });

        // 
        Event::listen(Failed::class, function (Failed $event): void {
            app(UserActivityLogger::class)->logAuth('auth.failed', [
                'auth_guard' => $event->guard,
                'attempted_email' => $event->credentials['email'] ?? null,
                'matched_user_id' => $event->user?->getAuthIdentifier(),
            ]);
        });
    }
}
