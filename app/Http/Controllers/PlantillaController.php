<?php

namespace App\Http\Controllers;

use App\Actions\PlantillasAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use RuntimeException;

class PlantillaController extends Controller
{
    public function index(Request $request)
    {
        $plantillasAction = app(PlantillasAction::class);
        $plantillas = $plantillasAction->listAll();
        $documentTypes = $plantillasAction->documentTypes();
        $isGestor = Auth::check() && Auth::user()->hasRole('gestor');
        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'plantillas' => $plantillas->map(function ($plantilla) {
                    return [
                        'id' => $plantilla->id,
                        'tipo_documento' => $plantilla->tipo_documento,
                        'prefijo' => $plantilla->prefijo,
                        'prefijo_mostrar' => $plantilla->display_prefijo,
                        'version' => $plantilla->version,
                        'archivo_url' => $plantilla->file_url,
                    ];
                })->values()->all(),
                'document_types' => $documentTypes,
                'is_gestor' => $isGestor,
            ]);
        }

        return view('plantillas', compact('plantillas', 'documentTypes', 'isGestor'));
    }

    public function create(Request $request)
    {
        $plantillasAction = app(PlantillasAction::class);
        $documentTypes = $plantillasAction->documentTypes();

        ////Se pide el tipo y un archivo adjunto compatible para la plantilla
        $data = $request->validate([
            'tipo_documento' => ['required', 'string', Rule::in($documentTypes)],
            'archivo' => ['required', 'file', 'mimes:doc,docx,pdf'],
        ]);

        try {
            $plantilla = $plantillasAction->create($data['tipo_documento'], $request->file('archivo'));
        } catch (RuntimeException $exception) {
            if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['plantilla' => $exception->getMessage()]);
        }

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => (bool) $plantilla, 'plantilla_id' => $plantilla->id]);
        }

        return redirect()
            ->route('plantillas.index')
            ->with('status', 'Plantilla creada correctamente.');
    }

    public function updateDocumentPlantilla(Request $request, $documentId)
    {
        $data = $request->validate([
            'plantilla_id' => ['required', 'integer', 'exists:plantillas,id'],
        ]);

        try {
            $document = app(PlantillasAction::class)->updateDocumentPlantilla((int) $documentId, (int) $data['plantilla_id']);
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'document_id' => $document->id,
            'plantilla_id' => $document->plantilla_id,
        ]);
    }
}