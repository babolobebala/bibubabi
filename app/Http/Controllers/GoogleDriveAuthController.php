<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GoogleDriveAuthController extends Controller
{
    public function __construct(
        protected GoogleDriveService $googleDriveService,
    ) {}

    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);

        $request->session()->put('google_drive_oauth_state', $state);

        $client = $this->googleDriveService->makeClient();
        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request): Response
    {
        $expectedState = (string) $request->session()->pull('google_drive_oauth_state');
        $currentState = $request->string('state')->toString();

        if ($expectedState === '' || $currentState !== $expectedState) {
            throw new HttpException(419, 'Google OAuth state mismatch.');
        }

        $code = $request->string('code')->toString();

        if ($code === '') {
            throw new HttpException(400, 'Authorization code is missing.');
        }

        $client = $this->googleDriveService->makeClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new HttpException(400, 'Failed to exchange authorization code for tokens.');
        }

        $refreshToken = $token['refresh_token'] ?? null;

        if (! is_string($refreshToken) || $refreshToken === '') {
            throw new HttpException(
                400,
                'Refresh token was not returned. Revoke the app access in your Google account, then retry the consent flow.'
            );
        }

        return response(
            "Copy this into your .env:\n\nGOOGLE_REFRESH_TOKEN={$refreshToken}\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }
}
