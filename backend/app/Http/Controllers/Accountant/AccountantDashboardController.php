<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountantDashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $query = DB::table('salaries')
            ->where('month', $month)
            ->where('year', $year);

        return response()->json([
            'total_payroll' => $query->sum('total'),
            'total_employees' => $query->count(),
            'calculated' => (clone $query)->where('status', 'calculated')->count(),
            'not_calculated' => (clone $query)->where('status', 'draft')->count(),

            'warnings' => [
                'missing_base_salary' => (clone $query)->whereNull('base_salary')->count(),
                'negative_salary' => (clone $query)->where('total', '<', 0)->count(),
            ],

            'status' => [
                'draft' => (clone $query)->where('status', 'draft')->count(),
                'calculated' => (clone $query)->where('status', 'calculated')->count(),
                'approved' => (clone $query)->where('status', 'approved')->count(),
                'published' => (clone $query)->where('status', 'published')->count(),
            ]
        ]);
    }
}
