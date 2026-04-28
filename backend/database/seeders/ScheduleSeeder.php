<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $data = [];

        for($i=0; $i<7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $data[] = [
                'employee_id' => 2,
                'work_schedule_id' => 1,
                'work_shift_id' => ($i % 3) + 1,
                'work_date' => $date,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('employee_schedule_assignments')->insert($data);
    }
}
