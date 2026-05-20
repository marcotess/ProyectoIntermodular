<?php

return [
    // Activa o desactiva toda la auditoria de actividad mediante variable de entorno.
    'enabled' => env('LOG_USER_ACTIVITY', true),

    'log_authenticated_http_requests' => env('LOG_USER_HTTP_ACTIVITY', true),

    // Se registran solo metodos que suelen implicar cambios de estado.
    // Las peticiones GET se excluyen para no saturar el log con navegacion normal.
    'http_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

    'ignored_routes' => [
        'login.submit',
        'logout',
    ],

    // Modelos del dominio auditados automaticamente mediante observer.
    'observed_models' => [
        App\Models\Course::class,
        App\Models\PR::class,
        App\Models\PRTeacher::class,
        App\Models\Document::class,
        App\Models\DocumentVariant::class,
        App\Models\DocumentStatusHistory::class,
        App\Models\DocumentNameAlias::class,
        App\Models\Plantilla::class,
    ],

    // Atributos que no deben incluirse en el contexto del log.
    'ignored_attributes' => [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ],

    // Canales de logging para los distintos tipos de actividad.
    'channels' => [
        'auth' => 'auth_activity',
        'model' => 'model_activity',
        'web' => 'web_activity',
    ],
];