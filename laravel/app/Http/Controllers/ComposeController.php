<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Jobs\SendEmailJob;
use App\Models\EmailAccount;
use App\Services\Mail\DraftService;
use App\Services\Mail\MailboxService;
use App\Services\Mail\Smtp\SendEmailService;

use Mews\Purifier\Facades\Purifier;

use Illuminate\Http\Request;

class ComposeController extends Controller
{   
   public function open(Request $request, EmailAccount $account, ?Email $draft = null){
        //Abort if the user requesting doesn't match the user id attached to the email account
        abort_unless($account->user_id === $request->user()->id, 403);

        if($draft){
            abort_unless($draft->email_account_id === $account->id && $draft->delivery_status === "draft", 404);
        }

         return view('mail.partials.compose-modal', [
            'account' => $account,
            'draft' => $draft,
        ]);
    }

    public function reply(Email $email) {
        return view('mail.partials.compose-modal', [
            'account' => $email->emailAccount,
            'draft' => null,

            'toRecipients' => [
                $email->from_email_address
            ],

            'subject' => 'Re: ' . ($email->subject ?? ''),
            'body' => '',
            'isInline' => true,
        ]);
    }

    public function replyAll(Email $email) {
        return view('mail.partials.compose-modal', [
            'account' => $email->emailAccount,
            'draft' => null,

            'toRecipients' => [
                $email->from_email_address
            ],

            'ccRecipients' => [],
            'subject' => 'Re: ' . ($email->subject ?? ''),
            'body' => '',
            'isInline' => true,
        ]);
    }

    public function forward(Email $email) {
        $safeHtml = $email->body_html
            ? Purifier::clean($email->body_html, 'email')
            : nl2br(e($email->body_text ?? $email->body_snippet ?? ''));

        $forwardedHtml = '
            <br><br>
            <div style="
                border-top:1px solid #d1d5db;
                margin-top:16px;
                padding-top:16px;
            ">
                <p><strong>---------- Forwarded message ---------</strong></p>

                <p>
                    <strong>From:</strong>
                    '.e($email->from_name ?: '').'
                    &lt;'.e($email->from_email_address).'&gt;
                </p>

                <p>
                    <strong>Date:</strong>
                    '.e($email->received_at?->format('M j, Y, H:i') ?? '').'
                </p>

                <p>
                    <strong>Subject:</strong>
                    '.e($email->subject ?? '(No subject)').'
                </p>

                <br>

                '.$safeHtml.'
            </div>
        ';

        return view('mail.partials.compose-modal', [
            'account' => $email->emailAccount,
            'draft' => null,

            'toRecipients' => [],
            'ccRecipients' => [],
            'bccRecipients' => [],

            'subject' => str_starts_with($email->subject ?? '', 'Fwd:')
                ? $email->subject
                : 'Fwd: ' . ($email->subject ?: '(No subject)'),

            'body' => '',
            'bodyHtml' => $forwardedHtml,
            'isInline' => true,
        ]);
    }

    public function save(Request $request, EmailAccount $account, DraftService $draftService){
        abort_unless($account->user_id === $request->user()->id, 403);

        $savedDraft = $this->validateAndSave($request, $account, $draftService);        

        //return a json response so that frontend can update without full page reload
        return response()->json([
            'success' => true,
            'message' => 'Draft saved successfully.',
            'action' => 'saved',
            'draft_id' => $savedDraft->id,
            'subject' => $savedDraft->subject,
            'body_text' => $savedDraft->body_text,
        ]);
    }

    public function send(Request $request, EmailAccount $account, DraftService $draftService, SendEmailService $sendService){
        abort_unless($account->user_id === $request->user()->id, 403);

        $draft = $this->validateAndSave($request, $account, $draftService);    
        $draft->loadMissing('recipients');

        $hasAnyRecipient = $draft->recipients->isNotEmpty();

        if (! $hasAnyRecipient) {
            return response()->json([
                'success' => false,
                'message' => 'At least one recipient is required.',
                'errors' => [
                    'recipients' => ['At least one recipient is required.'],
                ],
            ], 422);
        }

        $draft->update([
            'delivery_status' => 'pending',
        ]);

        SendEmailJob::dispatch($account->id, $draft->id);

        return response()->json([
            'success' => true,
            'action' => 'sent',
            'message' => 'Email queued for sending.',
        ]);
    }

    private function validateAndSave(Request $request, EmailAccount $account, DraftService $draftService): Email{
        $validated = $request->validate([
            'draft_id' => ['nullable', 'uuid'],
            'subject' => ['nullable', 'string', 'max:998'],
            'body_text' => ['nullable', 'string'],
            'body_html' => ['nullable', 'string'],
            'to' => ['nullable', 'array'],
            'to.*' => ['nullable', 'email'],
            'cc' => ['nullable', 'array'],
            'cc.*' => ['nullable', 'email'],
            'bcc' => ['nullable', 'array'],
            'bcc.*' => ['nullable', 'email'],
        ]);

        $draft = null;

        if(!empty($validated['draft_id'])){
            $draft = Email::where('id' , $validated['draft_id'])->where('email_account_id', $account->id)->where('delivery_status', 'draft')->firstOrFail();
        }

        $savedDraft = $draftService->saveDraft($account, $validated, $request->user(), $draft);
        return $savedDraft;
    }
}
