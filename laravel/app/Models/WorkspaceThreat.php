<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceThreat extends Model
{
    use HasUuids;
    protected $guarded =['id'];
    protected $casts = [
        'detected_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function emailAccount(): BelongsTo
    {
         return $this->belongsTo(Email::class, 'email_account_id');
    }

    public function email(): BelongsTo
    {
         return $this->belongsTo(Email::class, 'email_id');
    }

    public function securityScan(): BelongsTo
    {
        return $this->belongsTo(EmailSecurityScan::class, 'email_security_scan_id');
    }
}
