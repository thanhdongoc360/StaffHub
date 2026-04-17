<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryExport;

class AccountantSalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->select(
                'salaries.*',
                'users.name as user_name'
            );

        if ($request->month) {
            $query->where('salaries.month', $request->month);
        }

        if ($request->year) {
            $query->where('salaries.year', $request->year);
        }

        if ($request->status) {
            $query->where('salaries.status', $request->status);
        }

        if ($request->search) {
            $query->where('users.name', 'like', '%' . $request->search . '%');
        }

        return response()->json(
            $query->orderBy('salaries.id', 'desc')->paginate(10)
        );
    }

    public function calculate(Request $request)
    {
        Salary::where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'draft')
            ->get()
            ->each(function ($salary) {
                $salary->total = $salary->base_salary + $salary->bonus - $salary->tax;
                $salary->status = 'calculated';
                $salary->save();
            });

        return response()->json(['message' => 'Calculated']);
    }

    public function approve(Request $request)
    {
        Salary::where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'calculated')
            ->update(['status' => 'approved']);

        return response()->json(['message' => 'Approved']);
    }

    public function publish(Request $request)
    {
        Salary::where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'approved')
            ->update(['status' => 'published']);

        return response()->json(['message' => 'Published']);
    }

    public function show($id)
    {
        $salary = Salary::with('employee.user')->findOrFail($id);

        return response()->json($salary);
    }

    public function update(Request $request, $id)
    {
        $salary = Salary::findOrFail($id);

        //  không cho sửa nếu published
        if ($salary->status === 'published') {
            return response()->json([
                'message' => 'Cannot edit published salary'
            ], 403);
        }

        $request->validate([
            'base_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $salary->base_salary = $request->base_salary;
        $salary->bonus = $request->bonus ?? 0;
        $salary->tax = $request->tax ?? 0;
        $salary->note = $request->note;

        //  backend luôn là source of truth
        $salary->total = $salary->base_salary + $salary->bonus - $salary->tax;

        $salary->save();

        return response()->json($salary);
    }

    public function calculateOne($id)
    {
        $salary = Salary::findOrFail($id);

        if ($salary->status !== 'draft') {
            return response()->json(['message' => 'Chỉ tính khi là draft'], 400);
        }

        $salary->total = $salary->base_salary + $salary->bonus - $salary->tax;
        $salary->status = 'calculated';
        $salary->save();

        return response()->json($salary);
    }

    public function approveOne($id)
    {
        $salary = Salary::findOrFail($id);

        if ($salary->status !== 'calculated') {
            return response()->json(['message' => 'Phải tính trước'], 400);
        }

        $salary->status = 'approved';
        $salary->save();

        return response()->json($salary);
    }

    public function publishOne($id)
    {
        $salary = Salary::findOrFail($id);

        if ($salary->status !== 'approved') {
            return response()->json(['message' => 'Phải duyệt trước'], 400);
        }

        $salary->status = 'published';
        $salary->save();

        return response()->json($salary);
    }

    public function export(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer'
        ]);

        return Excel::download(
            new SalaryExport($request->month, $request->year),
            "salary_{$request->month}_{$request->year}.xlsx"
        );
    }

    public function create(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer'
        ]);

        $month = $request->month;
        $year = $request->year;

        // lấy employee đã có lương
        $existing = Salary::where('month', $month)
            ->where('year', $year)
            ->pluck('employee_id')
            ->toArray();

        // lấy employee chưa có
        $employees = DB::table('employees')
            ->whereNotIn('id', $existing)
            ->get();

        // ❌ nếu không còn ai để tạo
        if ($employees->isEmpty()) {
            return response()->json([
                'message' => 'Bảng lương tháng này đã được tạo rồi'
            ], 400);
        }

        // tạo bulk
        $data = [];

        foreach ($employees as $emp) {
            $data[] = [
                'employee_id' => $emp->id,
                'month' => $month,
                'year' => $year,
                'base_salary' => 0,
                'bonus' => 0,
                'tax' => 0,
                'total' => 0,
                'note' => '',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Salary::insert($data);

        return response()->json([
            'message' => 'Tạo bảng lương thành công'
        ]);
    }
}
