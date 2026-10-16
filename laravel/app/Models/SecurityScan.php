<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class SecurityScan extends Model
{
    use HasUuids;

    protected $table = 'email_security_scans';
    protected $guarded = [];

    public function email(): BelongsTo{
        return $this->BelongsTo(Email::class);
    }
}
