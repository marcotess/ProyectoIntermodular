<?php

namespace App\Http\Controllers;

use App\Actions\NotificacionAction;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index(Request $request, NotificacionAction $notificacionAction)
    {
        $notificaciones = $notificacionAction->listForUser(Auth::user());

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            return response()->json($notificaciones->map(function (Notificacion $notificacion) {
                return [
                    'id' => $notificacion->id,
                    'tema' => $notificacion->tema,
                    'mensaje' => $notificacion->mensaje,
                    'link' => $notificacion->link,
                    'fecha_envio' => optional($notificacion->fecha_envio)?->toISOString(),
                    'fecha_lectura' => optional($notificacion->fecha_lectura)?->toISOString(),
                ];
            })->values()->all());
        }

        return view('notificaciones', compact('notificaciones'));
    }

    public function open(Request $request, Notificacion $notificacion, NotificacionAction $notificacionAction): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $notificacion = $notificacionAction->markAsReadForUser($notificacion, Auth::user());

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'id' => $notificacion->id,
                'tema' => $notificacion->tema,
                'mensaje' => $notificacion->mensaje,
                'link' => $notificacion->link,
                'fecha_lectura' => optional($notificacion->fecha_lectura)?->toISOString(),
            ]);
        }

        return redirect()->to($notificacion->link ?: route('notificaciones.index'));
    }
}