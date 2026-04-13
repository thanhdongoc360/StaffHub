<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceCriteria extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 
        'description',
        'weight',
        'is_active'
    ];
}
