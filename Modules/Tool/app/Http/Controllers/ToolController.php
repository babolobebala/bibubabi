<?php

namespace Modules\Tool\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Tool\Http\Requests\GenerateTemplateDocumentRequest;
use Modules\Tool\Services\TemplateDocumentGeneratorService;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ToolController extends Controller
{
    public function geotagging(): Response
    {
        return Inertia::render(
            'tool::GeoTagging'
        );
    }

    public function documentGenerator(): Response
    {
        return Inertia::render(
            'tool::DocumentGenerator'
        );
    }

    public function documentGeneratorTemplate(): BinaryFileResponse
    {
        $templatePath = base_path('Modules/Tool/resources/assets/kosongan.docx');

        abort_unless(is_file($templatePath), 404);

        return response()->file($templatePath, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'inline; filename="kosongan.docx"',
        ]);
    }

    public function generateDocument(
        GenerateTemplateDocumentRequest $request,
        TemplateDocumentGeneratorService $templateDocumentGeneratorService,
    ): BinaryFileResponse|JsonResponse {
        $validated = $request->validated();

        try {
            $generatedPath = $templateDocumentGeneratorService->generate(
                $validated,
                $request->file('foto_satu'),
                $request->file('foto_dua'),
            );

            return response()->download(
                $generatedPath,
                $templateDocumentGeneratorService->buildDownloadFilename($validated),
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            )->deleteFileAfterSend();
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
