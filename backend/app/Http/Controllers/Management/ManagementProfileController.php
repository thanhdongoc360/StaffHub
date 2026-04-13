<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagementProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user()->load('employee');

        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'employee_code' => $user->employee->employee_code ?? null,
                'position' => $user->employee->position ?? null,
                'department' => $user->employee->department ?? null,
                'phone' => $user->employee->phone ?? null,
                'status' => $user->employee->status ?? null,
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20'
        ]);

        $user = $request->user();

        $user->update([
            'email' => $request->email
        ]);

        if ($user->employee) {
            $user->employee->update([
                'phone' => $request->phone
            ]);
        }

        return response()->json([
            'message' => 'Cập nhật hồ sơ thành công'
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
