<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SalaryExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->where('salaries.month', $this->month)
            ->where('salaries.year', $this->year)
            ->select(
                'users.name as employee',
                'salaries.base_salary',
                'salaries.bonus',
                'salaries.tax', 
                'salaries.total'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nhân viên',
            'Lương cơ bản',
            'Thưởng',
            'Thuế',
            'Tổng lương'
        ];
    }
}
