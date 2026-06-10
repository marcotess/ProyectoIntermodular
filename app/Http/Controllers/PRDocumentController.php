<?php

namespace App\Http\Controllers;

use App\Actions\AssignReviewerAction;
use App\Actions\CreateDocumentAction;
use App\Actions\PlantillasAction;
use App\Actions\UpdateDocumentAction;
use App\Models\PR;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

// este de aqui tiene bastante faena porque junta documentos, permisos, revisores y datos para la vista.
class PRDocumentController extends Controller
{
    public function index(Request $request, $prId)
    {
        $createDocumentAction = app(CreateDocumentAction::class);
        $plantillasAction = app(PlantillasAction::class);
        $reviewerAction = app(AssignReviewerAction::class);
        $updateDocumentAction = app(UpdateDocumentAction::class);
        $pr = PR::with(['documents.plantilla', 'documents.reviewers', 'documents.latestVariant.status', 'course'])->findOrFail($prId);
        $user = Auth::user();

        abort_unless($user->canAccessPr($pr), 403);

        $documents = $user->canViewAllDocumentsForPr($pr)
            ? $pr->documents
            : $pr->documents->filter(function (Document $document) use ($user) {
                return $document->reviewers->contains('id', $user->id);
            })->values();
        $availableReviewersByDocument = [];
        $variantsByDocument = [];
        $statusSummaryByDocument = [];
        $documentNamesByDocument = [];
        $plantillasByType = $plantillasAction->listGroupedByDocumentType();
        $documentTypes = $createDocumentAction->documentTypes();
        $temaEligibleTypes = $createDocumentAction->temaEligibleTypes();
        $variantStatuses = $updateDocumentAction->listVariantStatuses();
        $statusLabels = $createDocumentAction->statusLabels();
        $isGestor = $user->hasRole('gestor');
        $canEditTema = $user->hasAnyRole(['gestor', 'revisor']);
        $canRemoveVariants = $canEditTema;

        foreach ($documents as $document) {
            $availableReviewersByDocument[$document->id] = $reviewerAction->availableForDocument($document);
            $variantsByDocument[$document->id] = $createDocumentAction->listVariants($document);
            $statusSummaryByDocument[$document->id] = $createDocumentAction->summarizeVariantStatuses($document, $variantsByDocument[$document->id]);
            $documentNamesByDocument[$document->id] = $createDocumentAction->buildDisplayName($document, $pr);
        }

        if ($request->is('api/*') || $request->expectsJson() || $request->wantsJson()) {
            // Este payload reemplaza la información compuesta que antes solo consumía la vista doc.blade.php.
            return response()->json([
                'pr' => [
                    'id' => $pr->id,
                    'numero' => $pr->number,
                    'course_id' => $pr->course_id,
                    'course_name' => $pr->course?->name,
                ],
                'documents' => $documents->map(function (Document $document) use ($availableReviewersByDocument, $variantsByDocument, $statusSummaryByDocument, $documentNamesByDocument) {
                    return [
                        'id' => $document->id,
                        'type' => $document->type,
                        'tema' => $document->tema,
                        'nombre' => $documentNamesByDocument[$document->id] ?? $document->canonical_name,
                        'plantilla' => $document->plantilla ? [
                            'id' => $document->plantilla->id,
                            'tipo_documento' => $document->plantilla->tipo_documento,
                            'prefijo' => $document->plantilla->display_prefijo,
                        ] : null,
                        'reviewers' => $document->reviewers->map(fn ($reviewer) => [
                            'id' => $reviewer->id,
                            'name' => $reviewer->name,
                            'email' => $reviewer->email,
                        ])->values()->all(),
                        'available_reviewers' => collect($availableReviewersByDocument[$document->id] ?? [])->map(fn ($reviewer) => [
                            'id' => $reviewer->id,
                            'name' => $reviewer->name,
                            'email' => $reviewer->email,
                        ])->values()->all(),
                        'variants' => collect($variantsByDocument[$document->id] ?? [])->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'version' => $variant->version,
                                'status' => $variant->status ? [
                                    'id' => $variant->status->id,
                                    'name' => $variant->status->name,
                                ] : null,
                                'file_path' => $variant->file_path,
                            ];
                        })->values()->all(),
                        'status_summary' => $statusSummaryByDocument[$document->id] ?? [],
                    ];
                })->values()->all(),
                'plantillas_by_type' => $plantillasByType,
                'document_types' => $documentTypes,
                'tema_eligible_types' => $temaEligibleTypes,
                'variant_statuses' => $variantStatuses,
                'status_labels' => $statusLabels,
                'is_gestor' => $isGestor,
                'can_edit_tema' => $canEditTema,
                'can_remove_variants' => $canRemoveVariants,
            ]);
        }

        return view('doc', compact('pr', 'documents', 'availableReviewersByDocument', 'variantsByDocument', 'statusSummaryByDocument', 'documentNamesByDocument', 'plantillasByType', 'documentTypes', 'temaEligibleTypes', 'variantStatuses', 'statusLabels', 'isGestor', 'canEditTema', 'canRemoveVariants'));
    }

    public function createDocument(Request $request, $prId)
    {
        $createDocumentAction = app(CreateDocumentAction::class);
        $data = $request->validate([
            'type' => ['required', 'string', Rule::in($createDocumentAction->documentTypes())],
            'tema' => ['nullable', 'integer'],
        ]);

        try {
            $document = $createDocumentAction->createDocument($prId, $data['type'], Auth::id(), $data['tema'] ?? null);
        } catch (RuntimeException $exception) {
            if (! ($request->is('api/*') || $request->expectsJson() || $request->wantsJson())) {
                return back()
                    ->withInput()
                    ->withErrors(['document' => $exception->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        if (! ($request->is('api/*') || $request->expectsJson() || $request->wantsJson())) {
            return redirect()
                ->route('pr.documentos.index', ['pr' => $prId])
                ->with('status', 'Documento creado correctamente.');
        }

        return response()->json(['success' => (bool) $document, 'document_id' => $document->id]);
    }

    public function updateTema(Request $request, $documentId)
    {
        $document = Document::with(['pr.course', 'reviewers'])->findOrFail($documentId);

        abort_unless(Auth::user()->canAccessDocument($document), 403);

        $data = $request->validate([
            'tema' => ['nullable', 'integer'],
        ]);

        try {
            $document = app(UpdateDocumentAction::class)->updateTema((int) $documentId, $data['tema'] ?? null);
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'document_id' => $document->id,
            'tema' => $document->tema,
        ]);
    }

    public function addReviewer($documentId)
    {
        $document = Document::findOrFail($documentId);
        $data = request()->validate([
            'revisores' => 'required|array',
            'revisores.*' => 'integer|exists:users,id',
        ]);
        app(AssignReviewerAction::class)->assign($document, $data['revisores']);
        return response()->json(['success' => true]);
    }

    public function removeReviewer($documentId, $revisorId)
    {
        $document = Document::findOrFail($documentId);
        app(AssignReviewerAction::class)->remove($document, $revisorId);
        return response()->json(['success' => true]);
    }

    public function remove($documentId)
    {
        $result = app(UpdateDocumentAction::class)->remove($documentId);

        return response()->json(['success' => $result]);
    }
}
