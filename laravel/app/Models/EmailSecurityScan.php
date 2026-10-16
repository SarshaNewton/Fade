<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSecurityScan extends Model
{
    use HasUuids;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'score' => 'float',
        'security_reasons' => 'array',
        'scanned_at' => 'datetime',
    ];

    public function email(): BelongsTo{
        return $this->belongsTo(Email::class);
    }

    public function threat(): HasOne
    {
        return $this->hasOne(WorkspaceThreat::class, 'email_security_scan_id');
    }
}
