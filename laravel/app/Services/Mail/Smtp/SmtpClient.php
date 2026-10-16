<?php

namespace App\Services\Mail\Smtp;

use App\Models\Email;
use App\Models\EmailAccount;
use App\Services\Mail\EmailAccountTokenService;
use App\Services\Mail\Smtp\SmtpProviderManager;
use App\Services\Mail\Smtp\Contracts\SmtpProviderInterface;

use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class SmtpClient{
    
    protected Mailer $mailer;
    protected array $providers;
    protected EmailAccount $account;

    public function __construct(EmailAccount $account){
        $this->account = $account;
        $tokenService = new EmailAccountTokenService;
        $provider = (new SmtpProviderManager())->for($account);
        $transport = Transport::fromDsn($provider->buildDsn($account));
        $this->mailer = new Mailer($transport);
    }

    public function send(Email $draft){
        $draft->loadMissing('recipients');

         $toRecipients = $draft->recipients
            ->where('type', 'to')
            ->pluck('email_address')
            ->all();
        $ccRecipients = $draft->recipients
            ->where('type', 'cc')
            ->pluck('email_address')
            ->all();
        $bccRecipients = $draft->recipients
            ->where('type', 'bcc')
            ->pluck('email_address')
            ->all();
        
        if(empty($toRecipients) && empty($ccRecipients) && empty($bccRecipients)) {
            throw new \Exception('Cannot send email without recipients.');
        }

        $email = (new SymfonyEmail())
            ->from($this->account->email_address)
            ->subject($draft->subject ?? '')
            ->text($draft->body_text ?? '');

        if (!empty($draft->body_html)) {
            $email->html($draft->body_html);
        }

        foreach ($draft->recipients as $recipient) {
            if ($recipient->type === 'to') {
                $email->addTo($recipient->email_address);
            } elseif ($recipient->type === 'cc') {
                $email->addCc($recipient->email_address);
            } elseif ($recipient->type === 'bcc') {
                $email->addBcc($recipient->email_address);
            }
        }

        //If only bcc email
        if (empty($toRecipients) && empty($ccRecipients) && !empty($bccRecipients)) {
            $email->addTo($this->account->email_address);
        }
        $this->mailer->send($email);
    }
}
