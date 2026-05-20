<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Rutas comunes para cualquier usuario autenticado.
    Route::get('/home', [AuthController::class, 'profile'])->name('home');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/courses', [AuthController::class, 'courses'])->name('courses.index');
    Route::view('/tareas', 'tareas')->name('tasks.index');
    Route::get('/notificaciones', [App\Http\Controllers\NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/{notificacion}', [App\Http\Controllers\NotificacionController::class, 'open'])->name('notificaciones.open');

    // Navegacion y consulta del seguimiento por curso y PR.
    Route::get('/courses/{course}/prs', [App\Http\Controllers\PRController::class, 'index'])->name('courses.prs.index');
    Route::get('/courses/{course}/pr', [App\Http\Controllers\PRController::class, 'view'])->name('courses.pr.view');
    Route::get('/plantillas', [App\Http\Controllers\PlantillaController::class, 'index'])->name('plantillas.index');

    // Vista y operaciones de lectura sobre la documentacion del PR.
    Route::get('/pr/{pr}/documentos', [App\Http\Controllers\PRDocumentController::class, 'index'])->name('pr.documentos.index');

    // Actualizacion de estados de variantes accesible segun permisos de negocio.
    Route::post('/variant/{variant}/status/update', [App\Http\Controllers\VariantController::class, 'updateStatus'])->name('variant.status.update');

    Route::middleware('role:gestor')->group(function () {
        Route::post('/plantillas/create', [App\Http\Controllers\PlantillaController::class, 'create'])->name('plantillas.create');
        Route::post('/document/{document}/plantilla/update', [App\Http\Controllers\PlantillaController::class, 'updateDocumentPlantilla'])->name('document.plantilla.update');
        Route::post('/pr/{pr}/documentos/create', [App\Http\Controllers\PRDocumentController::class, 'createDocument'])->name('pr.documentos.create');
        Route::post('/document/{document}/variants/create', [App\Http\Controllers\VariantController::class, 'create'])->name('document.variants.create');
        Route::post('/document/{document}/remove', [App\Http\Controllers\PRDocumentController::class, 'remove'])->name('document.remove');
        Route::post('/document/{document}/revisores/add', [App\Http\Controllers\PRDocumentController::class, 'addReviewer'])->name('document.revisores.add');
        Route::post('/document/{document}/revisores/remove/{revisor}', [App\Http\Controllers\PRDocumentController::class, 'removeReviewer'])->name('document.revisores.remove');
        Route::post('/pr/{pr}/docentes/add', [App\Http\Controllers\PRDocenteController::class, 'add'])->name('pr.docentes.add');
        Route::post('/pr/{pr}/docentes/remove/{docente}', [App\Http\Controllers\PRDocenteController::class, 'remove'])->name('pr.docentes.remove');
        Route::post('/courses/{course}/pr/create', [App\Http\Controllers\PRController::class, 'create'])->name('courses.pr.create');
    });

    Route::middleware('role:gestor,revisor')->group(function () {
        Route::post('/document/{document}/tema/update', [App\Http\Controllers\PRDocumentController::class, 'updateTema'])->name('document.tema.update');
        Route::post('/variant/{variant}/remove', [App\Http\Controllers\VariantController::class, 'remove'])->name('variant.remove');
        Route::post('/pr/{pr}/fecha_limite/update', [App\Http\Controllers\PRDocenteController::class, 'updateFechaLimite'])->name('pr.fecha_limite.update');
        Route::post('/pr/{pr}/fase/update', [App\Http\Controllers\PRController::class, 'cambiarFase'])->name('pr.fase.update');
    });

});