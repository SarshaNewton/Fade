<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Services\Mail\Imap\InboxSyncService;

use App\Jobs\SyncInboxJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InboxController extends Controller
{
    public function sync(EmailAccount $account, InboxSyncService $syncService){
        $user = Auth::user();

        //DO NOT CONNECT TO ANOTHER USER'S ACCOUNT!
        if($account->user_id !== $user->id){
            abort(403);
        }

        try {
            SyncInboxJob::dispatch((string) $account->id);
            return redirect()->route('mail.index', ['account' => $account->id])->with('sync_started', 'Fade is fetching your latest emails in the background. You do not need to stay on this page.');
        } catch(\Throwable $error) {
            return redirect()->route('mail.index', ['account' => $account->id])->with('error', 'Inbox synced failed.', $error->getMessage());
        }
    }
     
}
