<?php

namespace App\Models;

use App\Services\Mail\Security\EmailEncryptionService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts =[
        'is_unread' => 'boolean',
        'is_starred' => 'boolean',
        'date_header_date' => 'datetime',
        'internal_date_date' => 'datetime',
        'received_at' => 'datetime',
        'headers' => 'array',
        'sent_at_date' => 'datetime',
        'read_at_date' => 'datetime',
        'imap_uid' => 'integer'
    ];

    // Relationships
    public function mailbox(): BelongsTo
    {
        return $this->belongsTo(Mailbox::class);
    }

    public function emailAccount(): BelongsTo
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(Recipient::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function securityScan(): HasOne
    {
        return $this->hasOne(EmailSecurityScan::class);
    }

    public function aiInsights(): HasMany
    {
        return $this->hasMany(EmailAiInsight::class);
    }

    public function latestAiInsight(): HasOne
    {
        return $this->hasOne(EmailAiInsight::class)->latest('created_at');
    }

    public function threat(): HasOne 
    {
        return $this->hasOne(WorkspaceThreat::class, 'email_id');
    }



    // Getters and Setters (Encryption & Decryption)
    public function setSubjectAttribute($value){
        $this->attributes['subject'] = app(EmailEncryptionService::class)->encrypt($value);
    }

    public function setBodySnippetAttribute($value){
        $this->attributes['body_snippet'] = app(EmailEncryptionService::class)->encrypt($value);
    }

    public function setBodyTextAttribute($value){
        $this->attributes['body_text'] = app(EmailEncryptionService::class)->encrypt($value);
    }

    public function setBodyHtmlAttribute($value){
        $this->attributes['body_html'] = app(EmailEncryptionService::class)->encrypt($value);
    }

    public function getSubjectAttribute($value){
        return app(EmailEncryptionService::class)->decrypt($value);
    }

    public function getBodySnippetAttribute($value){
        return app(EmailEncryptionService::class)->decrypt($value);
    }

    public function getBodyTextAttribute($value){
        return app(EmailEncryptionService::class)->decrypt($value);
    }

    public function getBodyHtmlAttribute($value){
        return app(EmailEncryptionService::class)->decrypt($value);
    }
}
