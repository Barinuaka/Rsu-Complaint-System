<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'role_name',
        'access_level',
    ];

    // A role can belong to many users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}