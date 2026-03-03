<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GoogleDrivePageController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('GoogleDrivePage', [
            'canConnectGoogleDrive' => $user instanceof User && $user->hasRole('super_admin'),
            'hasGoogleRefreshToken' => config('services.google.refresh_token') !== null
                && config('services.google.refresh_token') !== '',
            'googleDriveFolderId' => config('services.google.drive_folder_id'),
        ]);
    }
}
