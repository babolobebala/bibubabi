<?php

namespace Modules\Tool\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
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

    public function documentGeneratorDocx(): Response
    {
        return Inertia::render(
            'tool::DocumentGeneratorDocx'
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
}
