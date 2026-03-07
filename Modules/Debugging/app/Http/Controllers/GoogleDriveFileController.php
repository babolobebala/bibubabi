<?php

namespace Modules\Debugging\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\GoogleDriveListFilesRequest;
use App\Http\Requests\StoreGoogleDriveFileRequest;
use App\Http\Requests\UpdateGoogleDriveFileRequest;
use App\Services\GoogleDriveService;
use Illuminate\Http\JsonResponse;

class GoogleDriveFileController extends Controller
{
    public function __construct(
        protected GoogleDriveService $googleDriveService,
    ) {}

    public function index(GoogleDriveListFilesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $files = $this->googleDriveService->listFiles(
            (int) ($validated['page_size'] ?? 20),
            $validated['page_token'] ?? null,
        );

        return response()->json($files);
    }

    public function store(StoreGoogleDriveFileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $file = $this->googleDriveService->uploadFile(
            $request->file('file'),
            $validated['name'] ?? null,
            (bool) ($validated['make_public'] ?? false),
        );

        return response()->json([
            'message' => 'File berhasil diunggah ke Google Drive.',
            'item' => $file,
        ], 201);
    }

    public function update(UpdateGoogleDriveFileRequest $request, string $fileId): JsonResponse
    {
        $validated = $request->validated();

        $file = $this->googleDriveService->renameFile($fileId, $validated['name']);

        if ((bool) ($validated['make_public'] ?? false)) {
            $this->googleDriveService->makeFilePublic($fileId);
        }

        return response()->json([
            'message' => 'File Google Drive berhasil diperbarui.',
            'item' => $file,
        ]);
    }

    public function destroy(string $fileId): JsonResponse
    {
        $this->googleDriveService->deleteFile($fileId);

        return response()->json([
            'message' => 'File Google Drive berhasil dihapus.',
        ]);
    }
}
