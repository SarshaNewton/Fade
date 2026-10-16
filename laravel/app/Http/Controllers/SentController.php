<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;

use Illuminate\Http\Request;

class SentController extends Controller
{
    public function sent(Request $request, EmailAccount $account) {
        abort_unless($account->user_id === $request->user()->id, 403);

        $sentMailbox = $account->mailboxes()
            ->where('name', 'Sent')
            ->first();

        $emails = collect();

        if ($sentMailbox) {
            $emails = $sentMailbox->emails()
                ->with('recipients')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('mail.index', [
            'selectedAccount' => $account,
            'emails' => $emails,
            'mailbox' => $sentMailbox,
        ]);
    }
}
