<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewUserActivity;
use Illuminate\Http\Request;

class NotificationManagerController extends Controller
{
    public function subscribe(Request $req)
    {
        $authUser = $req->user();

        if (! $authUser instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $authUser->updatePushSubscription(
            $req->post('endpoint'),
            $req->post('public_key'),
            $req->post('auth_token'),
            $req->post('encoding'),
        );

        return response()->json(['message' => 'Subscribed!']);
    }

    public function unsubscribe(Request $req)
    {
        $authUser = $req->user();

        if (! $authUser instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $authUser->deletePushSubscription($req->post('endpoint'));

        return response()->json(['message' => 'Unsubscribed!']);
    }

    public function send(Request $req)
    {
        $user = $req->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->notify(new NewUserActivity);

        return redirect()->back();
    }
}
