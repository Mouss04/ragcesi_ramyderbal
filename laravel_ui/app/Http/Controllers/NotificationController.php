<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Return the unread notification count for polling.
     * Only counts notifications for comments that are still PENDING.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $pendingIds = Comment::where('status', 'PENDING')->pluck('id')->flip()->all();

        $allUnread = $user->unreadNotifications;

        // Auto-dismiss stale notifications (comment no longer pending)
        $staleIds = $allUnread
            ->filter(fn($n) => !isset($pendingIds[$n->data['comment_id'] ?? null]))
            ->pluck('id')
            ->all();
        if (!empty($staleIds)) {
            DB::table('notifications')->whereIn('id', $staleIds)->update(['read_at' => now()]);
        }

        $active = $allUnread->filter(fn($n) => isset($pendingIds[$n->data['comment_id'] ?? null]));
        $latest = $active->first();

        return response()->json([
            'count'  => $active->count(),
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
