<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceException extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'handled_by',
        'old_check_in',
        'old_check_out',
        'new_check_in',
        'new_check_out',
        'reason'
    ];


    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
