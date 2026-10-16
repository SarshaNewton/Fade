<?php

namespace App\Services\Security;

use App\Models\WorkspaceThreat;
use App\Models\EmailSecurityScan;

use Illuminate;

class WorkspaceThreatsService{

    public function save(EmailSecurityScan $scan): void {
        $email = $scan->email;
        $emailAccount = $email->emailAccount;
        $user = $emailAccount->user;
        $workspace = $user->workspaces()->first();

        WorkspaceThreat::updateOrCreate(
            [
                'email_id' => $email->id,
                'email_security_scan_id' => $scan->id,
            ],
            [
                'workspace_id' => $workspace->id,
                'email_account_id' => $emailAccount->id,
                'severity' => $scan->risk,
                'status' => 'new',
                'detected_at' => now(),
            ]
        );

    }
}