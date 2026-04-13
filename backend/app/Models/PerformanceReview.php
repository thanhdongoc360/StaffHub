<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id', 
        'reviewer_id', 
        'period_month', 
        'period_year',
        'kpi_score',
        'discipline_score',
        'collaboration_score',
        'growth_score',
        'total_score',
        'rank',
        'kpi_comment',
        'discipline_comment',
        'collaboration_comment',
        'reviewer_comment',
        'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function calculateTotalScore()
    {
        $kpi = $this->kpi_score ?? 0;
        $discipline = $this->discipline_score ?? 0;
        $collaboration = $this->collaboration_score ?? 0;
        $growth = $this->growth_score ?? 0;

        $total = $kpi * 0.4 + $discipline * 0.3 + $collaboration * 0.2 + $growth * 0.1;

        return round($total, 2);
    }

    public function calculateRank($totalScore)
    {
        if($totalScore >= 85) return 'A';
        if($totalScore >= 70) return 'B';
        if($totalScore >= 50) return 'C';
        return 'D';
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total_score = $model->calculateTotalScore();
            $model->rank = $model->calculateRank($model->total_score);
        });
    }

}
