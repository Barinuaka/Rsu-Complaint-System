<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintUpdate extends Model
{
    public $timestamps = false; // Has only updated_at, not created_at

    protected $fillable = [
        'complaint_id', 'updated_by', 'action_type',
        'previous_status', 'new_status',
        'update_note', 'updated_at',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}