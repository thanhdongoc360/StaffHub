<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'start_date',
        'end_date',
        'status'
    ];

    public function assignments()
    {
        return $this->hasMany(EmployeeScheduleAssignment::class);
    }
}
