<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Notification;

class LeaveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'required|string'
        ]);

        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin nhân viên'
            ], 422);
        }

        $leave = LeaveRequest::create([
            'employee_id' => $user->employee->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'Chờ duyệt'
        ]);

        return response()->json([
            'message' => 'Tạo đơn nghỉ phép thành công',
            'data' => $leave
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        }

        $leaves = LeaveRequest::where('employee_id', $user->employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $leaves
        ]);
    }

    public function adminIndex()
    {
        $leaves = LeaveRequest::with('employee.user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $leaves
        ]);
    }


    public function managementIndex(Request $request)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin nhân viên'
            ], 404);
        }

        $department = $user->employee->department;

        $leaves = LeaveRequest::with('employee.user')
            ->whereHas('employee', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $leaves
        ]);
    }

    public function managementApprove(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin nhân viên'
            ], 404);
        }

        $department = $user->employee->department;

        $leave = LeaveRequest::where('id', $id)
            ->whereHas('employee', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->first();

        if (!$leave) {
            return response()->json([
                'message' => 'Bạn không có quyền xử lý đơn này'
            ], 403);
        }

        if ($leave->status !== 'Chờ duyệt') {
            return response()->json([
                'message' => 'Đơn này đã được xử lý'
            ], 400);
        }

        $leave->update([
            'status' => 'Đã duyệt'
        ]);

        return response()->json([
            'message' => 'Duyệt đơn thành công'
        ]);
    }

    public function managementReject(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json(['message' => 'Không tìm thấy nhân viên'], 404);
        }

        $department = $user->employee->department;

        $leave = LeaveRequest::where('id', $id)
            ->whereHas('employee', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->first();

        if (!$leave) {
            return response()->json(['message' => 'Bạn không có quyền'], 403);
        }

        if ($leave->status !== 'Chờ duyệt') {
            return response()->json(['message' => 'Đơn đã xử lý'], 400);
        }

        $leave->update(['status' => 'Từ chối']);

        return response()->json(['message' => 'Từ chối thành công']);
    }
}
