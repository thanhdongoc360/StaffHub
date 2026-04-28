<?php

namespace App\Http\Controllers;

use App\Models\EmployeeScheduleAssignment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\WorkShift;
use App\Models\WorkSchedule;

class ScheduleController extends Controller
{
    public function mySchedule(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['message' => 'No employee'], 400);
        }

        $type = $request->type ?? 'week';

        $query = EmployeeScheduleAssignment::with('shift')
            ->where('employee_id', $employee->id);

        if ($type === 'today') {
            $query->whereDate('work_date', Carbon::today());
        }

        if ($type === 'week') {
            $query->whereBetween('work_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        if ($type === 'month') {
            $query->whereMonth('work_date', Carbon::now()->month)
                ->whereYear('work_date', Carbon::now()->year);
        }

        return response()->json(
            $query->orderBy('work_date')->get()
        );
    }

    public function getShifts()
    {
        return response()->json(
            WorkShift::orderBy('id', 'desc')->get()
        );
    }

    public function storeShift(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required'
        ]);

        $shift = WorkShift::create($request->all());

        return response()->json($shift);
    }

    public function updateShift(Request $request, $id)
    {
        $shift = WorkShift::findOrFail($id);

        $shift->update($request->all());

        return response()->json($shift);
    }

    public function deleteShift($id)
    {
        WorkShift::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function assignEmployees(Request $request)
    {
        $scheduleId = $request->schedule_id;
        $shiftId = $request->shift_id;
        $employeeIds = $request->employee_ids;
        $dates = $request->dates;

        foreach ($employeeIds as $employeeId) {
            foreach ($dates as $date) {

                //  tránh assign trùng
                $exists = EmployeeScheduleAssignment::where([
                    'employee_id' => $employeeId,
                    'work_date' => $date
                ])->exists();

                if (!$exists) {
                    EmployeeScheduleAssignment::create([
                        'employee_id' => $employeeId,
                        'work_schedule_id' => $scheduleId,
                        'work_shift_id' => $shiftId,
                        'work_date' => $date,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Assigned']);
    }

    public function managementView(Request $request)
    {
        $user = auth()->user();
        $department = $user->employee->department;

        return EmployeeScheduleAssignment::with(['shift', 'employee.user'])
            ->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->orderBy('work_date')
            ->get();
    }

    public function createWeekSchedule(Request $request)
    {
        $start = Carbon::parse($request->start_date);

        $schedule = WorkSchedule::create([
            'start_date' => $start,
            'end_date' => $start->copy()->addDays(6),
            'status' => 'draft'
        ]);

        return $schedule;
    }
}
