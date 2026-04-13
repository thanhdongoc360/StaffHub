<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\PerformanceReview;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');
        $search = $request->query('search');

        $user = auth()->user();

        // lấy employee của user login
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'User chưa liên kết employee'
            ], 400);
        }

        $department = $employee->department;

        $employees = Employee::where('department', $department)
            ->when($search, function ($q) use ($search) {
                $q->where('employee_code', 'like', "%$search%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            })
            ->with(['user', 'performanceReviews' => function ($q) use ($month, $year) {
                $q->where('period_month', $month)
                    ->where('period_year', $year);
            }])
            ->get();

        return $employees->map(function ($emp) {
            $review = $emp->performanceReviews->first();

            return [
                'id' => $emp->id,
                'name' => $emp->user->name ?? null,
                'code' => $emp->employee_code,
                'position' => $emp->position,

                'review_id' => $review->id ?? null,
                'status' => $review->status ?? 'not_reviewed',
                'total_score' => $review->total_score ?? null,
                'rank' => $review->rank ?? null,
            ];
        });
    }

    public function show(Request $request, $employeeId)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $user = auth()->user();
        $userEmployee = $user->employee;

        if (!$userEmployee) {
            return response()->json(['message' => 'No employee linked'], 400);
        }

        $department = $userEmployee->department;

        $employee = Employee::with('user')
            ->where('id', $employeeId)
            ->where('department', $department)
            ->firstOrFail();

        $review = PerformanceReview::where('employee_id', $employeeId)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if (!$review) {
            $review = PerformanceReview::create([
                'employee_id' => $employeeId,
                'reviewer_id' => $user->id,
                'period_month' => $month,
                'period_year' => $year,
                'status' => 'draft',
            ]);
        }

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->user->name ?? null,
                'code' => $employee->employee_code,
                'position' => $employee->position,
                'department' => $employee->department,
            ],
            'review' => [
                'id' => $review->id,
                'kpi_score' => $review->kpi_score ?? 0,
                'discipline_score' => $review->discipline_score ?? 0,
                'collaboration_score' => $review->collaboration_score ?? 0,
                'growth_score' => $review->growth_score ?? 0,
                'reviewer_comment' => $review->reviewer_comment ?? '',
                'total_score' => $review->total_score,
                'rank' => $review->rank,
                'status' => $review->status,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer',
            'year' => 'required|integer',

            'kpi_score' => 'nullable|integer|min:0|max:100',
            'discipline_score' => 'nullable|integer|min:0|max:100',
            'collaboration_score' => 'nullable|integer|min:0|max:100',
            'growth_score' => 'nullable|integer|min:0|max:100',
        ]);

        $user = auth()->user();
        $userEmployee = $user->employee;

        $employee = Employee::where('id', $request->employee_id)
            ->where('department', $userEmployee->department)
            ->firstOrFail();

        $review = PerformanceReview::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'period_month' => $request->month,
                'period_year' => $request->year,
            ],
            [
                'reviewer_id' => $user->id,
                'kpi_score' => $request->kpi_score,
                'discipline_score' => $request->discipline_score,
                'collaboration_score' => $request->collaboration_score,
                'growth_score' => $request->growth_score,

                'kpi_comment' => $request->kpi_comment,
                'discipline_comment' => $request->discipline_comment,
                'collaboration_comment' => $request->collaboration_comment,
                'reviewer_comment' => $request->reviewer_comment,

                'status' => $request->status ?? 'draft',
            ]
        );

        return response()->json($review->fresh());
    }

    public function confirm($id)
    {
        $user = auth()->user();

        if ($user->role_id !== 3) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review = PerformanceReview::findOrFail($id);

        if ($review->status !== 'submitted') {
            return response()->json([
                'message' => 'Chỉ confirm khi đã submitted'
            ], 400);
        }

        $review->status = 'confirmed';
        $review->save();

        return response()->json($review);
    }

    public function history($employeeId)
    {
        $user = auth()->user();
        $userEmployee = $user->employee;

        $employee = Employee::where('id', $employeeId)
            ->where('department', $userEmployee->department)
            ->firstOrFail();

        $reviews = PerformanceReview::where('employee_id', $employeeId)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->take(6)
            ->get();

        return response()->json($reviews);
    }
}
