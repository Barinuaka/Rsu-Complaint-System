<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Complaint extends Model
{
    protected $fillable = [
        'tracking_token', 'submitter_id', 'is_anonymous',
        'complaint_title', 'complaint_text', 'evidence_file_path',
        'category_id', 'urgency_level', 'nlp_confidence',
        'vader_compound_score', 'campus_id', 'assigned_to',
        'current_status', 'sla_deadline_at', 'escalation_count',
        'resolved_at', 'resolution_note', 'satisfaction_rating',
    ];

    protected $casts = [
        'is_anonymous'   => 'boolean',
        'sla_deadline_at'=> 'datetime',
        'resolved_at'    => 'datetime',
    ];

    // Auto-generate UUID tracking token on creation
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($complaint) {
            $complaint->tracking_token = (string) Str::uuid();
        });
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function assignedOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function updates()
    {
        return $this->hasMany(ComplaintUpdate::class);
    }

    public function anonymousVault()
    {
        return $this->hasOne(AnonymousVault::class);
    }

    // Check if SLA is breached
    public function isSlaBreached(): bool
    {
        return $this->sla_deadline_at &&
               now()->isAfter($this->sla_deadline_at) &&
               !in_array($this->current_status, ['resolved', 'closed']);
    }
}