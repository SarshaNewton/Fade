<?php

namespace App\Actions;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class CreateWorkspace
{
    public function handle(User $owner, string $name)
    {
        return DB::transaction(function() use($owner, $name){
            $workspace = Workspace::create([
                'name' =>$name,
                'owner_id' => $owner->id,
            ]);

            $workspace->users()->attach($owner->id, ['role' => 'owner']);

            return $workspace;
        });
    }
}
