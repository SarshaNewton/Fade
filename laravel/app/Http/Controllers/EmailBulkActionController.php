<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\Mailbox;
use App\Models\EmailAccount;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailBulkActionController extends Controller 
{
    public function index(Request $request) {
        $user = Auth::user();

        $activeMailboxRole = $request->get('mailbox', 'inbox');

        $allowedMailboxRoles = ['inbox', 'sent', 'drafts'];

        if (! in_array($activeMailboxRole, $allowedMailboxRoles, true)) {
            $activeMailboxRole = 'inbox';
        }

        $emailAccounts = $user->emailAccounts()
            ->with([
                'mailboxes.emails' => function ($query) use ($activeMailboxRole) {
                    $query
                        ->with(['attachments', 'recipients', 'securityScan', 'latestAiInsight'])
                        ->when($activeMailboxRole === 'sent', function ($query) {
                            $query->latest('sent_at');
                        })
                        ->when($activeMailboxRole !== 'sent', function ($query) {
                            $query->latest('received_at');
                        });
                },
            ])
            ->get();

        $accounts = auth()->user()->emailAccounts()->get();
        $selectedAccount = $this->resolveSelectedAccount($user, $emailAccounts, $request);
        $selectedEmail = null;

        if ($request->filled('email')) {
            $selectedEmail = Email::with([
                'attachments',
                'recipients',
                'securityScan',
                'aiInsights',
            ])
                ->where('id', $request->email)
                ->whereHas('mailbox.emailAccount', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->first();
        }

        return view('mail.index', [
            'user' => $user,
            'emailAccounts' => $emailAccounts,
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'selectedEmail' => $selectedEmail,
            'activeMailboxRole' => $activeMailboxRole,
        ]);
    }

    public function trash(Request $request, Email $email) {
        abort_unless(
            $email->emailAccount->user_id === $request->user()->id,
            403
        );

        $email->update([
            'delivery_status' => 'trashed',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email moved to trash.',
            ]);
        }

        return redirect()
            ->route('inbox.index')
            ->with('success', 'Email moved to trash.');
    }
    
    public function toggleStar(Request $request, Email $email) {
        abort_unless(
            $email->emailAccount->user_id === $request->user()->id,
            403
        );

        $email->update([
            'is_starred' => !$email->is_starred,
        ]);

        return response()->json([
            'success' => true,
            'starred' => $email->is_starred,
        ]);
    }
    
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:mark_read,mark_unread,star,unstar,trash'],
            'email_ids' => ['required', 'array', 'min:1'],
            'email_ids.*' => ['uuid', 'exists:emails,id'],
        ]);

        $emails = Email::whereIn('id', $validated['email_ids'])
            ->whereHas('emailAccount', function ($query) {
                $query->where('user_id', auth()->id());
            });

        match ($validated['action']) {
            'mark_read' => $emails->update([
                'is_unread' => false,
                'read_at' => now(),
            ]),

            'mark_unread' => $emails->update([
                'is_unread' => true,
                'read_at' => null,
            ]),

            'star' => $emails->update([
                'is_starred' => true,
            ]),

            'unstar' => $emails->update([
                'is_starred' => false,
            ]),

            'trash' => $this->bulkMoveToTrash($validated['email_ids']),
        };

        return back()->with('success', 'Bulk action applied.');
    }

    private function bulkMoveToTrash(array $emailIds): void
    {
        $emails = Email::whereIn('id', $emailIds)
            ->whereHas('emailAccount', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with('emailAccount')
            ->get();

        foreach ($emails as $email) {
            $trashMailbox = Mailbox::where('email_account_id', $email->email_account_id)
                ->where('role', 'trash')
                ->first();

            if ($trashMailbox) {
                $email->update([
                    'mailbox_id' => $trashMailbox->id,
                ]);
            }
        }
    }

     // Select the correct email account to show for the user
    private function resolveSelectedAccount($user, $emailAccounts, Request $request): EmailAccount {
        if ($request->filled('account')) {
            $account = $emailAccounts->firstWhere('id', $request->account);

            if ($account) {
                session(['selected_email_account_id' => $account->id]);
                return $account;
            }
        }

        if (session()->has('selected_email_account_id')) {
            $account = $emailAccounts->firstWhere('id', session('selected_email_account_id'));

            if ($account) {
                return $account;
            }
        }

        $emailAcc = $user->userPreference?->primary_email_account;

        return $emailAccounts->firstWhere('id', $emailAcc)
            ?? $emailAccounts->first();
    }

}
