<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'matric_number',
        'email',
        'phone_number',
        'password',
        'role_id',
        'campus_id',
        'department',
        'account_status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',  // Auto-bcrypt on save
        ];
    }

    // A user belongs to one role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // A user belongs to one campus
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // A user has submitted many complaints
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'submitter_id');
    }

    // A user has many assigned complaints (as handler)
    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }

    // Helper: check if user has minimum access level
    public function hasAccessLevel(int $level): bool
    {
        return $this->role && $this->role->access_level >= $level;
    }

    // Helper: check role by name
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }
}