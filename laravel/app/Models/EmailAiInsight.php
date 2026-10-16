<?php

namespace App\Models;

use App\Services\Mail\Security\EmailEncryptionService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAiInsight extends Model
{
    use HasUuids;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'summary' => 'encrypted:string',
        'action_needed' => 'encrypted:array',
        'secondary_intents' => 'array',
        'raw_response' => 'encrypted:array',
    ];

    public function email(): BelongsTo {
        return $this->belongsTo(Email::class);
    }

}