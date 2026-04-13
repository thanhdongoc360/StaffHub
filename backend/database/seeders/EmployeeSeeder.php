<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'user_id' => null,
                'employee_code' => 'IT001',
                'position' => 'Backend Developer',
                'department' => 'IT',
                'phone' => '0900000001',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'employee_code' => 'IT002',
                'position' => 'Frontend Developer',
                'department' => 'IT',
                'phone' => '0900000002',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'employee_code' => 'IT003',
                'position' => 'DevOps Engineer',
                'department' => 'IT',
                'phone' => '0900000003',
                'status' => 'active',
            ],
            [
                'user_id' => null,
                'employee_code' => 'HR001',
                'position' => 'HR Executive',
                'department' => 'HR',
                'phone' => '0900000004',
                'status' => 'active',
            ],
        ]);
    }
}
