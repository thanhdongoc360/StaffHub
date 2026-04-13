<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('performance_criteria')->insert([
            [
                'name' => 'KPI',
                'description' => 'Đánh giá hiệu suất công việc',
                'weight' => 40,
            ],
            [
                'name' => 'Kỷ luật',
                'description' => 'Tuân thủ nội quy',
                'weight' => 30,
            ],
            [
                'name' => 'Hợp tác',
                'description' => 'Làm việc nhóm',
                'weight' => 20,
            ],
            [
                'name' => 'Phát triển',
                'description' => 'Học hỏi và phát triển',
                'weight' => 10,
            ],
        ]);
    }
}
