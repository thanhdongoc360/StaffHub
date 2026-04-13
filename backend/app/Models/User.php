<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function getRoleAttribute($value)
    {
        return $this->roleRelation
            ? $this->roleRelation->name
            : $value;
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'reviewer_id');
    }

    // public function isAdmin()
    // {
    //     return $this->roleRelation?->name === 'admin';
    // }

    // public function isEmployee()
    // {
    //     return $this->roleRelation?->name === 'employee';
    // }
}
