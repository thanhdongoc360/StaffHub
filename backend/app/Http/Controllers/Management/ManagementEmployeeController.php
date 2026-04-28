<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\LeaveRequest;


class ManagementEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $management = $request->user();

        // Lấy phòng ban của management
        $department = $management->employee->department;

        // Lấy nhân viên cùng phòng ban
        $query = Employee::with('user')
            ->where('department', $department);

        if ($request->search) {
            $search = $request->search;
            $keyword = '%' . $search . '%';

            $query->where(function ($q) use ($keyword) {
                $q->where('employee_code', 'like', $keyword)
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->sort_by ?? 'id';
        $sortOrder = $request->sort_order ?? 'desc';

        if ($sortBy === 'name') {
            $query->join('users', 'employees.user_id', '=', 'users.id')
                ->orderBy('users.name', $sortOrder)
                ->select('employees.*');
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $employees = $query->paginate(15);

        return response()->json(
            [
                'department' => $department,
                'employees' => $employees,
            ]
        );
    }

    public function show(Request $request, $id)
    {
        $management = $request->user();
        $department = $management->employee->department;

        $employee = Employee::with('user')
            ->where('department', $department)
            ->findOrFail($id); // không tìm thấy thì tự trả về 404

        return response()->json($employee);
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json(['message' => 'Không có thông tin nhân viên'], 403);
        }

        $department = $user->employee->department;

        // Tổng nhân viên phòng ban
        $totalUsers = User::whereHas('employee', function ($q) use ($department) {
            $q->where('department', $department);
        })->count();

        // 5 nhân viên gần đây
        $recentUsers = User::with('employee')
            ->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->latest()
            ->take(5)
            ->get();

        // Đơn nghỉ chờ duyệt của phòng ban
        $pendingLeaves = LeaveRequest::where('status', 'Chờ duyệt')
            ->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->count();

        return response()->json([
            'total' => $totalUsers,
            'users' => $recentUsers,
            'pending_leaves' => $pendingLeaves
        ]);
    }
}
