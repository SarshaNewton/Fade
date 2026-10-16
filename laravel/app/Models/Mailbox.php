<?php

namespace App\Models;

use App\Models\Email;
use App\Models\EmailAccount;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mailbox extends Model
{
    use HasFactory, HasUuids;

    protected $guarded =[];

    public function emailAccount(): BelongsTo{
        return $this->belongsTo(EmailAccount::class);
    }

    public function emails(): HasMany{
        return $this->hasMany(Email::class);
    }
}
