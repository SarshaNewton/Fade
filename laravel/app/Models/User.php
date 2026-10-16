<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function socialAccounts(){
        return $this->hasMany(\App\Models\SocialAccount::class);
    }

    public function emailAccounts(){
        return $this->hasMany(\App\Models\EmailAccount::class);
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users')
            ->using(WorkspaceUser::class)
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

   
    public function userPreference(): HasOne
    {
        return $this->hasOne(\App\Models\UserPreference::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') : null,

            set: fn (?string $value) =>
                $value ? mb_strtolower(trim($value), 'UTF-8') : null,
        );
    }

    protected $keyType = 'string';
    public $incrementing = false;
}
