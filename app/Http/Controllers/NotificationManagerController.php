<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationManagerController extends Controller
{
    public function subscribe(Request $req)
    {
        $authUser = $req->user();

        if (! $authUser instanceof User) {
            Log::warning('Push subscribe rejected: unauthenticated request.', [
                'ip' => $req->ip(),
                'user_agent' => $req->userAgent(),
            ]);

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $authUser->updatePushSubscription(
                $req->post('endpoint'),
                $req->post('public_key'),
                $req->post('auth_token'),
                $req->post('encoding'),
            );

            Log::info('Push subscribe success.', [
                'user_id' => $authUser->getKey(),
                'endpoint' => $req->post('endpoint'),
                'encoding' => $req->post('encoding'),
                'host' => $req->getHost(),
            ]);
        } catch (Throwable $error) {
            Log::error('Push subscribe failed.', [
                'user_id' => $authUser->getKey(),
                'endpoint' => $req->post('endpoint'),
                'encoding' => $req->post('encoding'),
                'host' => $req->getHost(),
                'error' => $error->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to subscribe push notification.',
                'error' => $error->getMessage(),
            ], 500);
        }

        return response()->json(['message' => 'Subscribed!']);
    }

    public function unsubscribe(Request $req)
    {
        $authUser = $req->user();

        if (! $authUser instanceof User) {
            Log::warning('Push unsubscribe rejected: unauthenticated request.', [
                'ip' => $req->ip(),
                'user_agent' => $req->userAgent(),
            ]);

            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $authUser->deletePushSubscription($req->post('endpoint'));

            Log::info('Push unsubscribe success.', [
                'user_id' => $authUser->getKey(),
                'endpoint' => $req->post('endpoint'),
                'host' => $req->getHost(),
            ]);
        } catch (Throwable $error) {
            Log::error('Push unsubscribe failed.', [
                'user_id' => $authUser->getKey(),
                'endpoint' => $req->post('endpoint'),
                'host' => $req->getHost(),
                'error' => $error->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to unsubscribe push notification.',
                'error' => $error->getMessage(),
            ], 500);
        }

        return response()->json(['message' => 'Unsubscribed!']);
    }

    public function send(Request $req)
    {
        $user = $req->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $user->notify(new NewUserActivity);

            Log::info('Push send triggered.', [
                'user_id' => $user->getKey(),
                'host' => $req->getHost(),
            ]);
        } catch (Throwable $error) {
            Log::error('Push send failed.', [
                'user_id' => $user->getKey(),
                'host' => $req->getHost(),
                'error' => $error->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to send push notification.',
                'error' => $error->getMessage(),
            ], 500);
        }

        return redirect()->back();
    }
}
