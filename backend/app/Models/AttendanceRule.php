<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_start_time',
        'work_end_time',
        'late_threshold_minutes',
        'half_day_threshold_minutes',
        'standard_work_minutes'
    ];
}
