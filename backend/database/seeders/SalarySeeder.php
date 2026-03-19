<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('salaries')->insert([
            'employee_id' => 3,
            'base_salary' => 6000000,
            'bonus' => 500000,
            'total' => 6500000,
            'month' => 1,
            'year' => 2025,
            'note' => 'Lương tháng 12'
        ]);

        DB::table('salaries')->insert([
            'employee_id' => 3,
            'base_salary' => 5700000,
            'bonus' => 400000,
            'total' => 6100000,
            'month' => 2,
            'year' => 2025,
            'note' => 'Lương tháng 11'
        ]);
    }
}
