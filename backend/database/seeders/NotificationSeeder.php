<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->insert([
            'user_id' => 8,
            'title' => 'Họp team marketing',
            'content' => 'Họp theo lịch trình hằng ngày',
            'date' => '2026-04-05',
            'is_read' => false
        ]);
        DB::table('notifications')->insert([
            'user_id' => 8,
            'title' => 'Họp team marketing',
            'content' => 'Họp theo lịch trình hằng ngày',
            'date' => '2026-04-06',
            'is_read' => false
        ]);
        DB::table('notifications')->insert([
            'user_id' => 22,
            'title' => 'Họp team marketing',
            'content' => 'Họp theo lịch trình hằng ngày',
            'date' => '2026-04-06',
            'is_read' => false
        ]);
    }
}
