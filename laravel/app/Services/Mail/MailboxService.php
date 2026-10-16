<?php

namespace App\Services\Mail;

use App\Models\Mailbox;
use App\Models\EmailAccount;

class MailboxService{

    public function ensureDefaultMailboxes(EmailAccount $account): void{
        $this->ensureInbox($account);
        $this->ensureDraftbox($account);
        $this->ensureSentbox($account);
        $this->ensureSpambox($account);
        $this->ensureOutbox($account);
    }

    //Ensure EmailAccount has an inbox
    public function ensureInbox(EmailAccount $account): Mailbox{
        return Mailbox::firstOrCreate( 
            [
                'email_account_id' => $account->id,
                'imap_path' => 'INBOX'
            ],
            [
                'name'     =>'Inbox',
                'role'          => 'inbox',
                'parent_id'     => null,
                'is_system'     => true,
                'uid_validity'  => null,
                'last_seen_uid' => null
            ]
        );
    }

    //Ensure EmailAccount has a draftbox
    public function ensureDraftbox(EmailAccount $account): Mailbox{
        return Mailbox::firstOrCreate(
            [
                'email_account_id' => $account->id,
                'role' => 'draft'
            ],
            [
                'name' => 'Draft',
                'imap_path' => null,
                'parent_id' => null,
                'is_system' => true,
                'uid_validity' => null,
                'last_seen_uid' => null
            ]
        );
    }

    //Ensure EmailAccount has a sentbox
    public function ensureSentbox(EmailAccount $account): Mailbox{
        return Mailbox::firstOrCreate(
            [
                'email_account_id' => $account->id,
                'role' => 'sent'
            ],
            [
                'name' => 'Sent',
                'imap_path' => null,
                'parent_id' => null,
                'is_system' => true,
                'uid_validity' => null,
                'last_seen_uid' => null
            ]
        );
    }

    public function ensureSpambox(EmailAccount $account): Mailbox {
        return Mailbox::firstOrCreate(
            [
                'email_account_id' => $account->id,
                'role' => 'spam',
            ],
            [
                'name' => 'Spam',
                'imap_path' => null,
                'parent_id' => null,
                'is_system' => true,
                'uid_validity' => null,
                'last_seen_uid' => null
            ]
        );
    }

    public function ensureOutbox(EmailAccount $account): Mailbox {
        return Mailbox::firstOrCreate(
            [
                'email_account_id' => $account->id,
                'role' => 'outbox',
            ],
            [
                'name' => 'Outbox',
                'imap_path' => null,
                'parent_id' => null,
                'is_system' => true,
                'uid_validity' => null,
                'last_seen_uid' => null
            ]
        );
    }
}