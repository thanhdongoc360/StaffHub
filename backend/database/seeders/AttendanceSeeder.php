<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $today = Carbon::today();

        DB::table('attendances')->insert([
            [
                'employee_id' => 3,
                'date' => $today,
                'check_in_time' => $today->copy()->setTime(8, 0),
                'check_out_time' => $today->copy()->setTime(17, 0),
                'status' => 'present',
                'working_minutes' => 480
            ],
            [
                'employee_id' => 4,
                'date' => $today,
                'check_in_time' => $today->copy()->setTime(8, 20),
                'check_out_time' => $today->copy()->setTime(17, 0),
                'status' => 'late',
                'working_minutes' => 460
            ],
            [
                'employee_id' => 5,
                'date' => $today,
                'check_in_time' => $today->copy()->setTime(9, 0),
                'check_out_time' => $today->copy()->setTime(12, 0),
                'status' => 'half_day',
                'working_minutes' => 180
            ]
        ]);
    }
}
