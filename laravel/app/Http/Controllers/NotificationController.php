<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return JSON list of notifications (latest 30) + unread count.
     * Called by the frontend via AJAX/polling.
     */
    public function index()
    {
        $userId = Auth::id();

        $notifications = Notification::forUser($userId)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get(['id', 'type', 'title', 'message', 'icon', 'link', 'is_read', 'created_at']);

        $unreadCount = Notification::forUser($userId)->unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Notification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark ALL notifications for the current user as read.
     */
    public function markAllRead()
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Return only the unread count (lightweight poll).
     */
    public function unreadCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();

        return response()->json(['unread_count' => $count]);
    }
}