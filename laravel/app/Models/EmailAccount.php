<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAccount extends Model
{
    use HasUuids;

    protected $guarded =['id'];
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];
    
    protected $casts =[
        'expires_at' => 'datetime',
        'is_primary' => 'boolean',
        'last_synced_at' => 'datetime'
    ];

    public function user():  BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mailboxes(): HasMany{
        return $this->hasMany(Mailbox::class);
    }

    public function emails(): HasMany{
        return $this->hasMany(Email::class);
    }

    public function draftMailbox()
    {
        return $this->hasOne(Mailbox::class)->where('role', 'draft');
    }

    public function inboxMailbox()
    {
        return $this->hasOne(Mailbox::class)->where('role', 'inbox');
    }

    public function threats(): HasMany
    {
        return $this->hasMany(WorkspaceThreat::class, 'email_account_id');
    }
}
