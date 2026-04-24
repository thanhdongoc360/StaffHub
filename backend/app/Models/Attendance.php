<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'working_minutes',
        'overtime_minutes',
        'note'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function exceptions()
    {
        return $this->hasMany(AttendanceException::class);
    }
}
