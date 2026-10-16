<?php

namespace App\Services\Mail\Imap;

use App\Models\Email;
use App\Models\Recipient;
use App\Models\EmailAccount;
use App\Services\Mail\MailboxService;
use App\Services\Mail\Imap\ImapEmailPersistor;
use App\Services\Mail\Imap\ImapProviderManager;
use App\Services\Mail\Imap\Contracts\ImapProviderInterface;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\ClientManager;

class ImapClient{
    
    public function __construct(protected MailboxService $mailboxService, protected ImapEmailPersistor $imapEmailPersister){}

    protected function connect(EmailAccount $account) {
        
        $provider = app(ImapProviderManager::class)->for($account);
        $clientManager = new ClientManager();

        $client = $clientManager->make(
            $provider->getConfig($account)
        );

        $client->connect();

        return $client;
    }

    //Syncs the fade inbox with the provider returning the amount of emails synced
    public function syncInbox(EmailAccount $account, string $folderName): array{
        //Ensure that an inbox exists for the user in the database
        $mailbox = $this->mailboxService->ensureInbox($account);

        //Connect to IMAP
        $client = $this->connect($account);

        //Get Inbox mailbox and all the emails in it
        $folder = $client->getFolder($folderName);
        $messages = $folder->messages()->all()->setFetchFlags(true)->leaveUnread()->setFetchOrder('desc')->limit(20)->get();

        $syncedEmailIds = [];

        foreach ($messages as $message) {
            $email = $this->imapEmailPersister->save(
                account: $account,
                mailbox: $mailbox,
                message: $message,
            );

            $syncedEmailIds[] = $email->id;
        }
        return $syncedEmailIds;
    }
}
