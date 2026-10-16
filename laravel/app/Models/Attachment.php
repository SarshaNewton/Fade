<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasUuids;

    protected $table = 'email_attachments';
    protected $guarded = ['id'];

    public function email(): BelongsTo{
        return $this->BelongsTo(Email::class);
    }
}
