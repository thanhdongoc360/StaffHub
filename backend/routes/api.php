<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Management\ManagementEmployeeController;
use App\Http\Controllers\Management\ManagementProfileController;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManagementEmployeesExport;
use App\Http\Controllers\Accountant\AccountantDashboardController;
use App\Http\Controllers\Accountant\AccountantSalaryController;
use App\Http\Controllers\PerformanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::middleware('role:employee')->group(function () {
        Route::post('/leaves', [LeaveController::class, 'store']);
        Route::get('/leaves', [LeaveController::class, 'index']);

        Route::get('/my-salaries', [SalaryController::class, 'mySalaries']);

        Route::get('/employee/profile', [EmployeeController::class, 'profile']);
        Route::put('/employee/profile', [EmployeeController::class, 'updateProfile']);
        Route::put('/employee/change-password', [EmployeeController::class, 'changePassword']);

    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard']);
        Route::get('/users', [UserController::class, 'index']);

        Route::get('/employees', [UserController::class, 'employeeList']);
        Route::post('/employees', [UserController::class, 'store']);

        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::get('/leaves', [LeaveController::class, 'adminIndex']);
        Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve']);
        Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject']);

        Route::get('/salaries', [SalaryController::class, 'adminIndex']);
        Route::post('/salaries', [SalaryController::class, 'store']);

        Route::get('/profile', [AdminProfileController::class, 'profile']);
        Route::put('/profile', [AdminProfileController::class, 'updateProfile']);
        Route::put('/change-password', [AdminProfileController::class, 'changePassword']);

        Route::get('/performance', [PerformanceController::class, 'index']);
        Route::get('/performance/{employeeId}', [PerformanceController::class, 'show']);
        Route::get('/performance/history/{employeeId}', [PerformanceController::class, 'history']);
    });

    Route::middleware('role:management')->prefix('management')->group(function () {
        Route::get('/employees', [ManagementEmployeeController::class, 'index']);
        Route::get('employees/{id}', [ManagementEmployeeController::class, 'show']);
        Route::get('employees-export', function (Request $request) {
            return Excel::download(
                new ManagementEmployeesExport(
                    $request->search,
                    $request->status,
                    $request->sort_by,
                    $request->sort_order
                ),
                'employee.xlsx'
            );
        });

        Route::get('/profile', [ManagementProfileController::class, 'profile']);
        Route::put('/profile', [ManagementProfileController::class, 'updateProfile']);
        Route::put('/change-password', [ManagementProfileController::class, 'changePassword']);

        Route::get('/leaves', [LeaveController::class, 'managementIndex']);
        Route::post('/leaves/{id}/approve', [LeaveController::class, 'managementApprove']);
        Route::post('/leaves/{id}/reject', [LeaveController::class, 'managementReject']);

        Route::get('/salaries', [SalaryController::class, 'managementIndex']);

        Route::get('/dashboard', [ManagementEmployeeController::class, 'dashboard']);

        Route::get('/performance', [PerformanceController::class, 'index']);
        Route::get('/performance/{employeeId}', [PerformanceController::class, 'show']);
        Route::post('/performance', [PerformanceController::class, 'store']);
        Route::put('/performance/{id}/confirm', [PerformanceController::class, 'confirm']);
        Route::get('/performance/history/{employeeId}', [PerformanceController::class, 'history']);
    });

    Route::middleware('role:accountant')->prefix('accountant')->group(function () {
        Route::get('/dashboard', [AccountantDashboardController::class, 'index']);

        Route::get('/salaries', [AccountantSalaryController::class, 'index']);
        Route::post('/salary/calculate', [AccountantSalaryController::class, 'calculate']);
        Route::post('/salary/approve', [AccountantSalaryController::class, 'approve']);
        Route::post('/salary/publish', [AccountantSalaryController::class, 'publish']);
        Route::post('/salary/create', [AccountantSalaryController::class, 'create']);

        Route::get('/salary/{id}', [AccountantSalaryController::class, 'show']);
        Route::put('/salary/{id}', [AccountantSalaryController::class, 'update']);

        Route::post('/salary/{id}/calculate', [AccountantSalaryController::class, 'calculateOne']);
        Route::post('/salary/{id}/approve', [AccountantSalaryController::class, 'approveOne']);
        Route::post('/salary/{id}/publish', [AccountantSalaryController::class, 'publishOne']);
        Route::post('/salary/export', [AccountantSalaryController::class, 'export']);

        Route::get('/profile', [EmployeeController::class, 'profile']);
        Route::put('/profile', [EmployeeController::class, 'updateProfile']);
        Route::put('/change-password', [EmployeeController::class, 'changePassword']);

        Route::post('/leaves', [LeaveController::class, 'store']);
        Route::get('/leaves', [LeaveController::class, 'index']);
    });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
