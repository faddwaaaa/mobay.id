<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Return JSON list of notifications (latest 30) + unread count.
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

    /**
     * Delete a single notification.
     */
    public function destroy($id)
{
    $notification = \App\Models\Notification::where('id', $id)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $notification->delete();

    return redirect()->back()->with('success', 'Notifikasi berhasil dihapus');
}

public function destroyAll()
{
    \App\Models\Notification::where('user_id', auth::id())->delete();

    return redirect()->back()->with('success', 'Semua notifikasi dihapus');
}

    public function page(Request $request)
{
    $userId = Auth::id();
    $perPage = 15;
 
    $query = Notification::forUser($userId)
        ->orderByDesc('created_at');
 
    // Search
    if ($q = $request->input('q')) {
        $query->where(function ($sub) use ($q) {
            $sub->where('title', 'like', "%{$q}%")
                ->orWhere('message', 'like', "%{$q}%");
        });
    }
 
    // Filter tipe
    if ($type = $request->input('type')) {
        $query->where('type', $type);
    }
 
    $notifications = $query->paginate($perPage)->withQueryString();
    $unreadCount   = Notification::forUser($userId)->unread()->count();
 
    return view('notifications.index', compact('notifications', 'unreadCount'));
}

}