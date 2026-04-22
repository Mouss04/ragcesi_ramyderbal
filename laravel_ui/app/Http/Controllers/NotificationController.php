<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return the unread notification count for polling.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user   = $request->user();
        $count  = $user->unreadNotifications()->count();
        $latest = $user->unreadNotifications()->latest()->first();

        return response()->json([
            'count'  => $count,
            'latest' => $latest ? $latest->data : null,
        ]);
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
