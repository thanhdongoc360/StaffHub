<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if($user->role === 'admin') {
            $notifications = Notification::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        }

        return response()->json([
            'data' => $notifications
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = Notification::findOrFail($id);

        if($user->role !== 'admin' && $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized', 403]);
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true
            ]);
        }

        return response()->json(['message' => 'Đã đọc']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        if($user->role === 'admin') {
            Notification::where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);
        }

        return response()->json([
            'message' => 'Đã đọc tất cả'
        ]);
    }
}
