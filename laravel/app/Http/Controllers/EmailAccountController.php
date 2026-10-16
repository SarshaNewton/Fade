<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use Illuminate\Http\Request;

class EmailAccountController extends Controller
{
    public function index() {
        return view('accounts.index');
    }

    public function switch(EmailAccount $account) {
        abort_unless($account->user_id === auth()->id(), 403);

        session(['selected_email_account_id' => $account->id]);

        return redirect()->route('mail.index', [
            'account' => $account->id,
            'mailbox' => 'inbox',
        ]);
    }

    public function disconnect(EmailAccount $emailAccount) {
        abort_unless($emailAccount->user_id === auth()->id(), 403);
        //Add code for safe deliton if the account is primary
        $emailAccount->delete();

        $nextAccount = auth()->user()->emailAccounts()->first();

        if ($nextAccount) {
            session(['selected_email_account_id' => $nextAccount->id]);

            return redirect()->route('mail.index', [
                'account' => $nextAccount->id,
                'mailbox' => 'inbox',
            ]);
        }

        session()->forget('selected_email_account_id');

        return redirect()->route('dashboard');
    }
}
