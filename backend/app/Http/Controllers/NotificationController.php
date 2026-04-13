<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ADMIN → xem tất cả
        if ($user->role === 'admin') {
            $notifications = Notification::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // MANAGEMENT → xem thông báo nhân viên phòng ban
        elseif ($user->role === 'management') {

            if (!$user->employee) {
                return response()->json(['data' => []]);
            }

            $department = $user->employee->department;

            $notifications = Notification::with('user')
                ->whereHas('user.employee', function ($q) use ($department) {
                    $q->where('department', $department);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // EMPLOYEE → chỉ xem của mình
        else {
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
        $notification = Notification::with('user.employee')->findOrFail($id);

        // ADMIN → được phép
        if ($user->role === 'admin') {
            $notification->update(['is_read' => true]);
            return response()->json(['message' => 'Đã đọc']);
        }

        // MANAGEMENT → chỉ nếu cùng phòng ban
        if ($user->role === 'management') {

            if (!$user->employee) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $department = $user->employee->department;

            if ($notification->user->employee->department !== $department) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $notification->update(['is_read' => true]);
            return response()->json(['message' => 'Đã đọc']);
        }

        // EMPLOYEE → chỉ của mình
        if ($notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Đã đọc']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            Notification::where('is_read', false)
                ->update(['is_read' => true]);
        } elseif ($user->role === 'management') {

            if (!$user->employee) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $department = $user->employee->department;

            Notification::whereHas('user.employee', function ($q) use ($department) {
                $q->where('department', $department);
            })
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'message' => 'Đã đọc tất cả'
        ]);
    }
}
