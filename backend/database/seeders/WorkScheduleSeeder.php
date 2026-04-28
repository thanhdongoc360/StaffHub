<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('work_schedules')->insert([
           [
            'start_date' => Carbon::now()->startOfWeek(),
            'end_date' => Carbon::now()->endOfWeek(),
            'status' => 'draft'
            ] 
        ]); 

    }
}
