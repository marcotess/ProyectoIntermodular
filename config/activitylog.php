<?php

return [
    // activar y desactivar toda la auditoria de acciones medianteb una variable de entorno
    'enabled' => env('LOG_USER_ACTIVITY', true),

    
    'log_authenticated_http_requests' => env('LOG_USER_HTTP_ACTIVITY', true),

    // Solo interesan metodos que suelen implicar una cambio.Los get se dejan
    // fuera para no llenar el log con navegacion normal por la interfaz
    'http_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],

    
    'ignored_routes' => [
        'login.submit',
        'logout',
    ],

    // modelos del dominio que queremos auditar automaticamente mediante observer
    // 
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

    // atributos que no deben aparecer en el contexto del log
    'ignored_attributes' => [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ],

    //   canales de logging para diferentes tipos de actividad
    'channels' => [
        'auth' => 'auth_activity',
        'model' => 'model_activity',
        'web' => 'web_activity',
    ],
];