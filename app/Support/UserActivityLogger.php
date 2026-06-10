<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Servicio central de auditoría de acciones de usuario
 *
 * 
 */
class UserActivityLogger
{
    /**
     * 
     */
    public function logAuth(string $event, array $context = []): void
    {
        if (! config('activitylog.enabled')) {
            return;
        }
        Log::channel($this->channel('auth'))->info($this->message($event), $this->mergeContext($context));
    }
    public function logModel(string $event, Model $model, array $context = []): void
    {
        if (! config('activitylog.enabled') || ! Auth::check()) {
            return;
        }

        $modelContext = [
            'model_type' => class_basename($model),
            'model_id' => $model->getKey(),
        ];

        Log::channel($this->channel('model'))->info(
            $this->message($event, class_basename($model)),
            $this->mergeContext(array_merge($modelContext, $context))
        );
    }

    /**
     * registra una petición web autenticada que ha cambiado estado
     *
     * 
     */
    public function logWebAction(Request $request, array $context = []): void
    {
        if (! config('activitylog.enabled') || ! config('activitylog.log_authenticated_http_requests') || ! $request->user()) {
            return;
        }

        Log::channel($this->channel('web'))->info(
            $this->message('http.request'),
            $this->mergeContext(array_merge($this->requestContext($request), $context), $request)
        );
    }

    /**
     * elimina del contexto los atributos sensibles o irrelevantes definidos en
     * 
     */
    public function sanitizedAttributes(array $attributes): array
    {
        $ignored = config('activitylog.ignored_attributes', []);

        return Arr::except($attributes, $ignored);
    }

    /**
     * Construye un diff compacto para actualizaciones de modelos.
     *
     * Se guarda el valor previo y el nuevo únicamente en campos realmente
     * modificados, ignorando atributos excluidos por configuración
     */
    public function diffForUpdate(Model $model): array
    {
        $changes = [];

        foreach ($model->getChanges() as $attribute => $newValue) {
            if (in_array($attribute, config('activitylog.ignored_attributes', []), true)) {
                continue;
            }

            $changes[$attribute] = [
                'old' => $model->getOriginal($attribute),
                'new' => $newValue,
            ];
        }

        return $changes;
    }

    /**
     * añade al contexto la informacion comuncomo el usuario, IP,
     * metodo, nombre de ruta y URL completa
     */
    private function mergeContext(array $context, ?Request $request = null): array
    {
        $request ??= request();
        $user = Auth::user();

        return array_filter(array_merge([
            'user_id' => $user?->getAuthIdentifier(),
            'user_email' => $user?->email,
            'ip' => $request?->ip(),
            'method' => $request?->method(),
            'route' => $request?->route()?->getName(),
            'url' => $request?->fullUrl(),
        ], $context), static fn ($value) => $value !== null && $value !== []);
    }

    /**
     * extrae el contexto especifico de una peticion http
     *
     * S
     */
    private function requestContext(Request $request): array
    {
        return [
            'route_parameters' => $request->route()?->parameters() ?? [],
            'payload' => $this->sanitizedAttributes($request->except(['_token', 'password', 'password_confirmation'])),
        ];
    }


    private function channel(string $key): string
    {
        return config("activitylog.channels.{$key}", 'stack');
    }

    /**
     * nombre del mensaje
     */
    private function message(string $event, ?string $subject = null): string
    {
        return $subject === null
            ? "user_activity.{$event}"
            : "user_activity.{$event}.{$subject}";
    }
}