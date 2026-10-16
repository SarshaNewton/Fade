<?php

namespace App\Actions;
use App\Actions\CreateWorkspace;
use App\Models\User;
use App\Models\WorkspaceInvitation;

class HandleWorkspaceOnboarding
{
    public function handle(User $user, string $email): void {
        $hasInvite = WorkspaceInvitation::where('invited_email', $email)
            ->whereNull('accepted_at')
            ->exists();

        if ($hasInvite) {
            WorkspaceInvitation::where('invited_email', $email)
                ->whereNull('accepted_at')
                ->get()
                ->each(function ($invite) use ($user) {
                    $invite->workspace->users()->syncWithoutDetaching([
                        $user->id => ['role' => $invite->role],
                    ]);

                    $invite->accepted_at = now();
                    $invite->save();
                });

            return;
        }

        app(CreateWorkspace::class)->handle($user, 'Personal');
    }
}