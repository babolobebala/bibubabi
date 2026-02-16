<?php

namespace App\Http\Controllers;

use App\Http\Requests\MarkNotificationsReadRequest;
use App\Http\Requests\NotificationHistoryRequest;
use App\Models\User;
use App\Notifications\NewUserActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class NotificationManagerController extends Controller
{
    public function subscribe(Request $req): JsonResponse
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

    public function unsubscribe(Request $req): JsonResponse
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

    public function send(Request $req): RedirectResponse|JsonResponse
    {
        $authUser = $req->user();

        if (! $authUser instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $target = $req->query('target', 'self');
        $role = $req->query('role', 'super_admin');

        try {
            if ($target === 'all') {
                $users = User::query()
                    ->whereHas('pushSubscriptions')
                    ->get();

                Notification::send($users, new NewUserActivity);

                Log::info('Push send triggered for all users.', [
                    'triggered_by' => $authUser->getKey(),
                    'users_count' => $users->count(),
                    'host' => $req->getHost(),
                ]);
            } elseif ($target === 'role') {
                $users = User::query()
                    ->role($role)
                    ->whereHas('pushSubscriptions')
                    ->get();

                Notification::send($users, new NewUserActivity);

                Log::info('Push send triggered for role.', [
                    'triggered_by' => $authUser->getKey(),
                    'role' => $role,
                    'users_count' => $users->count(),
                    'host' => $req->getHost(),
                ]);
            } else {
                $authUser->notify(new NewUserActivity);

                Log::info('Push send triggered for self.', [
                    'user_id' => $authUser->getKey(),
                    'host' => $req->getHost(),
                ]);
            }
        } catch (Throwable $error) {
            Log::error('Push send failed.', [
                'user_id' => $authUser->getKey(),
                'target' => $target,
                'role' => $role,
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

    public function history(NotificationHistoryRequest $request): JsonResponse
    {
        $authUser = $request->user();

        if (! $authUser instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validated();
        $limit = (int) ($validated['limit'] ?? 20);

        $notifications = $authUser->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at?->toIso8601String(),
                    'created_at' => $notification->created_at?->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'items' => $notifications,
            'unread_count' => $authUser->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(MarkNotificationsReadRequest $request): JsonResponse
    {
        $authUser = $request->user();

        if (! $authUser instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validated();
        $ids = $validated['ids'] ?? [];

        $query = $authUser->unreadNotifications();

        if (is_array($ids) && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }

        $updatedCount = $query->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notifications updated.',
            'updated_count' => $updatedCount,
            'unread_count' => $authUser->unreadNotifications()->count(),
        ]);
    }
}
