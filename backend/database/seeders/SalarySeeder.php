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

            // ===== DRAFT =====
            
            [
                'employee_id' => 2,
                'base_salary' => 12000000,
                'bonus' => 1000000,
                'tax' => 500000,
                'total' => 0,
                'month' => 4,
                'year' => 2026,
                'note' => 'Chưa tính',
                'status' => 'draft'
            ],

            // ===== CALCULATED =====
            [
                'employee_id' => 3,
                'base_salary' => 15000000,
                'bonus' => 2000000,
                'tax' => 1500000,
                'total' => 15500000,
                'month' => 4,
                'year' => 2026,
                'note' => 'Đã tính',
                'status' => 'calculated'
            ],

            // ===== APPROVED =====
            [
                'employee_id' => 4,
                'base_salary' => 20000000,
                'bonus' => 3000000,
                'tax' => 2000000,
                'total' => 21000000,
                'month' => 4,
                'year' => 2026,
                'note' => 'Đã duyệt',
                'status' => 'approved'
            ],

            // ===== PUBLISHED =====
            [
                'employee_id' => 5,
                'base_salary' => 18000000,
                'bonus' => 1000000,
                'tax' => 1000000,
                'total' => 18000000,
                'month' => 4,
                'year' => 2026,
                'note' => 'Đã công bố',
                'status' => 'published'
            ],

            // ===== LỖI TEST WARNING =====
            [
                'employee_id' => 6,
                'base_salary' => 0,
                'bonus' => 1000000,
                'tax' => 500000,
                'total' => 0,
                'month' => 4,
                'year' => 2026,
                'note' => 'Thiếu base salary',
                'status' => 'draft'
            ],
            [
                'employee_id' => 7,
                'base_salary' => 5000000,
                'bonus' => 0,
                'tax' => 6000000,
                'total' => -1000000,
                'month' => 4,
                'year' => 2026,
                'note' => 'Lương âm',
                'status' => 'calculated'
            ]

        ]);
    }
}
