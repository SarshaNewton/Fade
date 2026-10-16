<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WorkspaceUser extends Pivot
{
    use HasUuids;
    
    protected $table = 'workspace_users';
    protected $guarded =['id'];
    
    public $incrementing = false;
    protected $keyType = 'string';   
}
