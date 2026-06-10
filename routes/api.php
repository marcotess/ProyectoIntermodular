<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// esta entrada API es la parte mas util para peticiones JS y clientes externos, aunque ahora siga bastante recogida.
// /api/login devuelve un token Sanctum; el resto de rutas /api exigen Bearer token válido.
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('api.logout');

// Todas estas rutas responden JSON y quedan protegidas por auth:sanctum.
Route::middleware('auth:sanctum')->group(function () {
        Route::get('/courses', [AuthController::class, 'home'])->name('api.courses.index');
        Route::get('/notificaciones', [App\Http\Controllers\NotificacionController::class, 'index'])->name('api.notificaciones.index');
        Route::get('/notificaciones/{notificacion}', [App\Http\Controllers\NotificacionController::class, 'open'])->name('api.notificaciones.open');
        Route::get('/plantillas', [App\Http\Controllers\PlantillaController::class, 'index'])->name('api.plantillas.index');
        Route::get('/courses/{course}/prs', [App\Http\Controllers\PRController::class, 'index'])->name('api.courses.prs.index');
        Route::get('/courses/{course}/pr', [App\Http\Controllers\PRController::class, 'view'])->name('api.courses.pr.view');
        Route::get('/pr/{pr}/documentos', [App\Http\Controllers\PRDocumentController::class, 'index'])->name('api.pr.documentos.index');
        Route::post('/variant/{variant}/status/update', [App\Http\Controllers\VariantController::class, 'updateStatus'])->name('api.variant.status.update');

        Route::middleware('role:gestor')->group(function () {
            // Operaciones de gestión que además de autenticación requieren rol gestor.
            Route::post('/plantillas/create', [App\Http\Controllers\PlantillaController::class, 'create'])->name('api.plantillas.create');
            Route::post('/document/{document}/plantilla/update', [App\Http\Controllers\PlantillaController::class, 'updateDocumentPlantilla'])->name('api.document.plantilla.update');
            Route::post('/pr/{pr}/documentos/create', [App\Http\Controllers\PRDocumentController::class, 'createDocument'])->name('api.pr.documentos.create');
            Route::post('/document/{document}/variants/create', [App\Http\Controllers\VariantController::class, 'create'])->name('api.document.variants.create');
            Route::post('/document/{document}/remove', [App\Http\Controllers\PRDocumentController::class, 'remove'])->name('api.document.remove');
            Route::post('/document/{document}/revisores/add', [App\Http\Controllers\PRDocumentController::class, 'addReviewer'])->name('api.document.revisores.add');
            Route::post('/document/{document}/revisores/remove/{revisor}', [App\Http\Controllers\PRDocumentController::class, 'removeReviewer'])->name('api.document.revisores.remove');
            Route::post('/pr/{pr}/docentes/add', [App\Http\Controllers\PRDocenteController::class, 'add'])->name('api.pr.docentes.add');
            Route::post('/pr/{pr}/docentes/remove/{docente}', [App\Http\Controllers\PRDocenteController::class, 'remove'])->name('api.pr.docentes.remove');
            Route::post('/courses/{course}/pr/create', [App\Http\Controllers\PRController::class, 'create'])->name('api.courses.pr.create');
        });

        Route::middleware('role:gestor,revisor')->group(function () {
            // Acciones compartidas entre gestor y revisor, también consumidas como JSON.
            Route::post('/document/{document}/tema/update', [App\Http\Controllers\PRDocumentController::class, 'updateTema'])->name('api.document.tema.update');
            Route::post('/variant/{variant}/remove', [App\Http\Controllers\VariantController::class, 'remove'])->name('api.variant.remove');
            Route::post('/pr/{pr}/fecha_limite/update', [App\Http\Controllers\PRDocenteController::class, 'updateFechaLimite'])->name('api.pr.fecha_limite.update');
            Route::post('/pr/{pr}/nombre/update', [App\Http\Controllers\PRController::class, 'updateNombre'])->name('api.pr.nombre.update');
            Route::post('/pr/{pr}/fase/update', [App\Http\Controllers\PRController::class, 'cambiarFase'])->name('api.pr.fase.update');
        });
});