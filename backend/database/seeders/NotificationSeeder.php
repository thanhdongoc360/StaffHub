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
            'user_id' => 29,
            'title' => 'Họp team kế toán',
            'content' => 'Họp theo lịch trình hằng ngày',
            'date' => '2026-04-05',
            'is_read' => false
        ]);
        DB::table('notifications')->insert([
            'user_id' => 29,
            'title' => 'Nghỉ phép',
            'content' => 'Nghỉ phép',
            'date' => '2026-04-07',
            'is_read' => false
        ]);
        DB::table('notifications')->insert([
            'user_id' => 29,
            'title' => 'Về sớm',
            'content' => 'Về sớm',
            'date' => '2026-04-08',
            'is_read' => false
        ]);
    }
}
