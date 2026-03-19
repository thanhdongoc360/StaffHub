<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee')
                    ->where('name', '!=', 'admin')
                    ->get();

        return response()->json([
            'total' => $users->count(),
            'users' => $users
        ]);  
    }  

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'position' => 'required',
            'department' => 'required',
            'password' => 'required|min:6',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employee'
            ]);

            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'EMP' . time(),
                'position' => $request->position,
                'department' => $request->department,
                'status' => $request->status
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Tạo nhân viên thành công'
            ], 201);
        }
        catch(\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if($user->employee) {
            $user->employee->position = $request->position;
            $user->employee->department = $request->department;
            $user->employee->save();
        }

        return response()->json([
            'message' => 'User update successfully',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'Đã xóa tài khoản thành công'
        ]);
    }

    public function user(Request $request)
    {
        return $request->user()->load('employee');
    }

    public function employeeList()
    {
        $employee = Employee::with('user')->get();

        return response()->json($employee);
    }
}
