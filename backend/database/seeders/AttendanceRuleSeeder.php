<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attendance_rules')->insert([
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'late_threshold_minutes' => 15,
            'half_day_threshold_minutes' => 240,
            'standard_work_minutes' => 480,
        ]);
    }
}
