<?php

namespace App\Http\Controllers;

use App\Actions\CreateVariantAction;
use App\Actions\UpdateDocumentAction;
use App\Models\Document;
use App\Models\DocumentVariant;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class VariantController extends Controller
{
    public function create($documentId)
    {
        $document = Document::with(['plantilla', 'pr.course'])->findOrFail($documentId);

        try {
            $variant = app(CreateVariantAction::class)->create($document, Auth::id());
        } catch (RuntimeException $exception) {
           
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json(['success' => (bool) $variant, 'variant_id' => $variant->id]);
    }

    public function remove($variantId)
    {
        $variant = DocumentVariant::with(['document.pr.course', 'document.reviewers'])->findOrFail($variantId);

        abort_unless(Auth::user()->canAccessVariant($variant), 403);

        $result = app(UpdateDocumentAction::class)->removeVariant($variantId);

        return response()->json(['success' => $result]);
    }

    public function updateStatus($variantId)
    {
        $variant = DocumentVariant::with(['document.pr.course', 'document.reviewers'])->findOrFail($variantId);

        abort_unless(Auth::user()->canAccessVariant($variant), 403);

        $statusId = request()->input('status_id');

        try {
            $result = app(UpdateDocumentAction::class)->updateVariantStatus($variantId, $statusId);
        } catch (RuntimeException $exception) {
            // falla si se intenta cambiar a un estado que ya esta ocupado por otra variante del mismodocumento
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json(['success' => $result]);
    }
}