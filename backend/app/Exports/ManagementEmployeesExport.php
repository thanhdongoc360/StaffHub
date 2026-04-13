<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class ManagementEmployeesExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $status;
    protected $sortBy;
    protected $sortOrder;

    public function __construct($search, $status, $sortBy, $sortOrder)
    {
        $this->search = $search;
        $this->status = $status;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
    }

    public function collection()
    {
        $management = Auth::user();
        if (!$management->employee) {
            abort(403, 'Không có phòng ban');
        }

        $department = $management->employee->department;

        $query = Employee::with('user')
            ->where('department', $department);

        if ($this->search) {
            $keyword = '%' . $this->search . '%';

            $query->where(function ($q) use ($keyword) {
                $q->where('employee_code', 'like', $keyword)
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->sortBy === 'name') {
            $query->join('users', 'employees.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortOrder)
                ->select('employees.*');
        } else {
            $query->orderBy($this->sortBy, $this->sortOrder);
        }

        return $query->get()->map(function ($employee) {  // get: lay nhieu employee, first: lay cai dau
            return [
                $employee->user->name,
                $employee->user->email,
                $employee->employee_code,
                $employee->position,
                $employee->department,
                $employee->status === 'active' ? 'Đang làm' : 'Nghỉ việc'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Họ tên',
            'Email',
            'Mã nhân viên',
            'Vị trí',
            'Phòng ban',
            'Trạng thái'
        ];
    }
}
