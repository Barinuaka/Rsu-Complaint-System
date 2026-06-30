<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutingRule extends Model
{
    protected $fillable = [
        'category_id', 'campus_id', 'urgency_level',
        'primary_handler_role_id', 'escalation_handler_role_id',
        'secondary_escalation_role_id', 'sla_hours',
        'notify_central_admin',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function primaryHandlerRole()
    {
        return $this->belongsTo(Role::class, 'primary_handler_role_id');
    }

    public function escalationHandlerRole()
    {
        return $this->belongsTo(Role::class, 'escalation_handler_role_id');
    }
}