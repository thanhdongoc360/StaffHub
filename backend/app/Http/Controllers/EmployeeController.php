<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function profile(Request $request) 
    {
        $user = $request->user()->load('employee');

        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'employee_code' => $user->employee->employee_code,
                'position' => $user->employee->position,
                'department' => $user->employee->department,
                'phone' => $user->employee->phone,
                'status' => $user->employee->status
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email'
        ]);

        $user->update([
            'email' => $request->email
        ]);

        $user->employee->update([
            'phone' => $request->phone
        ]);

        return response()->json([
            'message' => 'Cập nhật thành công'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $user = $request->user();

        if(!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 422);
        }

        $user->update([
            'password' => bcrypt($request->new_password)
        ]);

        return response()->json([
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
