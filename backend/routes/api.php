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
        Route::get('/dashboard', [UserController::class, 'index']);
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
    });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
