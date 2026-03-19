<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function profile(Request $request) 
    {
        $user = $request->user()->load('employee');

        return response()->json([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => optional($user->employee)->phone
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20'
        ]);

        $user = $request->user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if($user->employee) {
            $user->employee->update([
                'phone' => $request->phone
            ]);
        }

        return response()->json([
            'message' => 'Cập nhật thành công'
        ]);
    }

    public function changePassword(Request $request) 
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
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
