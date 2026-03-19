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
            'user_id' => 1,
            'employee_code' => 'adm199360',
            'position' => 'Admin',
            'department' => 'Quản trị',
            'phone' => '0388.888.888',
            'status' => 'Hoạt động'
        ]);
    }
}
