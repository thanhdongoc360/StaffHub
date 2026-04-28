<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('work_shifts')->insert([
            [
                'name' => 'Sáng',
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'break_minutes' => 0,
            ],
            [
                'name' => 'Chiều',
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 0,
            ],
            [
                'name' => 'Full',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 60,
            ],
        ]);
    }
}
