<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scheduleId = 1;

        DB::table('employee_schedule_assignments')->insert([
            [
                'employee_id' => 3,
                'work_schedule_id' => $scheduleId,
                'work_shift_id' => 3,
                'work_date' => Carbon::today(),
            ],
            [
                'employee_id' => 4,
                'work_schedule_id' => $scheduleId,
                'work_shift_id' => 1,
                'work_date' => Carbon::today(),
            ],
        ]);
    }
}
