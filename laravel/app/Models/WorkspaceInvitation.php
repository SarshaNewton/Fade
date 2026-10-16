<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkspaceInvitation extends Model
{
    use HasUuids;
    protected $guarded =['id'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function invitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
