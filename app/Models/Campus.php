<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'campus_name',
        'campus_code',
        'is_main_campus',
        'local_admin_id',
    ];

    // A campus has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A campus has many complaints
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    // A campus has many routing rules
    public function routingRules()
    {
        return $this->hasMany(RoutingRule::class);
    }
}