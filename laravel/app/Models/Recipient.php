<?php

namespace App\Models;

use App\Models\Email;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipient extends Model
{
    use HasUuids;

    protected $table = 'email_recipients';
    protected $guarded = [];

    public const TYPE_TO = 'to';
    public const TYPE_CC = 'cc';
    public const TYPE_BCC = 'bcc';

    public function email(): BelongsTo{
        return $this->belongsTo(Email::class);
    }
}
