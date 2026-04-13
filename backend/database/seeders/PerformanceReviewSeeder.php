<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PerformanceReview;

class PerformanceReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PerformanceReview::insert([
            [
                'employee_id' => 3,
                'reviewer_id' => 8,
                'period_month' => 3,
                'period_year' => 2026,
                'status' => 'submitted',
                'kpi_score' => 85,
                'discipline_score' => 90,
                'collaboration_score' => 80,
                'growth_score' => 88,
                'total_score' => 86,
            ],
            [
                'employee_id' => 4,
                'reviewer_id' => 8,
                'period_month' => 3,
                'period_year' => 2026,
                'status' => 'draft',
                'kpi_score' => 70,
                'discipline_score' => 75,
                'collaboration_score' => 78,
                'growth_score' => 80,
                'total_score' => 76,
            ],
            [
                'employee_id' => 5,
                'reviewer_id' => 8,
                'period_month' => 3,
                'period_year' => 2026,
                'status' => 'submitted',
                'kpi_score' => 92,
                'discipline_score' => 88,
                'collaboration_score' => 85,
                'growth_score' => 90,
                'total_score' => 89,
            ],
        ]);
    }
}
