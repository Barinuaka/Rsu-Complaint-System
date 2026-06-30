<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnonymousVault extends Model
{
    public $timestamps = false; // We use vault_created_at instead

    protected $fillable = [
        'complaint_id',
        'encrypted_identity',
        'encryption_key_ref',
        'authentication_tag',
        'disclosure_log',
        'vault_created_at',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}