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
                'employee_code' => 'AC001',
                'position' => 'Kế toán',
                'department' => 'accounting',
                'phone' => 'ACT',
                'status' => 'active',
                'user_id' => 29
            ]
        ]);
    }
}
