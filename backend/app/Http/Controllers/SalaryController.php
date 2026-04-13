<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function mySalaries(Request $request)
    {
        $query = Auth::user()
            ->employee
            ->salaries()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        return response()->json($query->get());
    }

    public function adminIndex(Request $request)
    {
        $query = Salary::with('employee.user')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $salary = Salary::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'year' => $request->year,
            'base_salary' => $request->base_salary,
            'bonus' => $request->bonus ?? 0,
            'total' => $request->base_salary + ($request->bonus ?? 0),
            'note' => $request->note ?? ''
        ]);

        return response()->json($salary->load('employee.user'));
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

        $query = Salary::with('employee.user')
            ->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($request->month) {
            $query->where('month', $request->month);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        return response()->json($query->get());
    }
}
