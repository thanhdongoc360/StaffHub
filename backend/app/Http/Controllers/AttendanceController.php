<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRule;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $today = Carbon::today();

        $exists = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if ($exists && $exists->check_in_time) {
            return response()->json([
                'message' => 'Bạn đã check-in rồi'
            ], 400);
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today
            ],
            [
                'check_in_time' => now()
            ]
        );

        return response()->json($attendance);
    }

    // ✅ CHECK OUT
    public function checkOut()
    {
        $user = auth()->user();
        $employee = $user->employee;

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_time) {
            return response()->json([
                'message' => 'Bạn chưa check-in'
            ], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'message' => 'Bạn đã check-out rồi'
            ], 400);
        }

        $attendance->check_out_time = now();

        // 🔥 tính thời gian làm
        $workingMinutes = Carbon::parse($attendance->check_in_time)
            ->diffInMinutes($attendance->check_out_time);

        // 🔥 lấy rule
        $rule = AttendanceRule::first();

        $attendance->working_minutes = $workingMinutes;

        // 🔥 tính OT
        if ($workingMinutes > $rule->standard_work_minutes) {
            $attendance->overtime_minutes =
                $workingMinutes - $rule->standard_work_minutes;
        }

        // 🔥 tính status
        $checkIn = Carbon::parse($attendance->check_in_time);
        $startTime = Carbon::parse($rule->work_start_time);

        if ($checkIn->gt($startTime)) {
            $lateMinutes = $startTime->diffInMinutes($checkIn);

            if ($lateMinutes <= $rule->late_threshold_minutes) {
                $attendance->status = 'late';
            } else {
                $attendance->status = 'half_day';
            }
        } else {
            $attendance->status = 'present';
        }

        $attendance->save();

        return response()->json($attendance);
    }

    // ✅ LỊCH SỬ
    public function myAttendance(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        $month = $request->month;
        $year = $request->year;

        $query = Attendance::where('employee_id', $employee->id);

        if ($month) {
            $query->whereMonth('date', $month);
        }

        if ($year) {
            $query->whereYear('date', $year);
        }

        return response()->json(
            $query->orderByDesc('date')->get()
        );
    }

    public function managementIndex(Request $request)
    {
        $user = auth()->user();

        if(!$user->employee) {
            return response()->json(['message' => 'No employee'], 400);
        }

        $department = $user->employee->department;

        $month = $request->month;
        $year = $request->year;
        $status = $request->status;
        $search = $request->search;

        $query = Attendance::query()
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->where('employees.department', $department)
            ->select(
                'attendances.*',
                'users.name',
                'employees.employee_code'
            );

        if($month) {
            $query->whereMonth('date', $month);
        }

        if($year) {
            $query->whereYear('date', $year);
        }

        if($status) {
            $query->where('attendances.status', $status);
        }

        if($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                    ->orWhere('employees.employee_code', 'like', "%$search%");
            });
        }

        return response()->json(
            $query->orderByDesc('date')->get()
        );
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if($user->role !== 'management') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date',
            'status' => 'nullable|string',
            'note' => 'nullable|string'
        ]);

        $attendance->check_in_time = $request->check_in_time;
        $attendance->check_out_time = $request->check_out_time;
        $attendance->status = $request->status;
        $attendance->note = $request->note;

        if($attendance->check_in_time && $attendance->check_out_time) {
            $attendance->working_minutes = Carbon::parse($attendance->check_in_time)
                ->diffInMinutes($attendance->check_out_time);
        } 

        $attendance->save();

        return response()->json($attendance);
    }
}
