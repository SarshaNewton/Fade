<?php

namespace App\Livewire;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AccountSwitcher extends Component
{
    public $accounts;
    public ?EmailAccount $selectedAccount = null;

    public string $nickname = '';

    public array $iconOptions = [
        'mail',
        'briefcase',
        'graduation-cap',
        'heart',
        'star',
        'crown',
        'user',
    ];

    public array $colorOptions = [
        '#354E96',
        '#EF38F5',
        '#F58438',
        '#3BB273',
        '#7300FF',
        '#22C1DC',
        '#000000',
    ];

    public function mount(?EmailAccount $selectedAccount = null): void {
        $this->loadAccounts($selectedAccount);
    }

    public function loadAccounts(?EmailAccount $selectedAccount = null): void {
        $this->accounts = Auth::user()->emailAccounts()->get();

        $this->selectedAccount = $selectedAccount
            ?? $this->accounts->firstWhere('id', session('selected_email_account_id'))
            ?? $this->accounts->first();

        if ($this->selectedAccount) {
            session(['selected_email_account_id' => $this->selectedAccount->id]);
        }

        $this->nickname = $this->selectedAccount?->nickname
            ?? ucfirst($this->selectedAccount?->provider ?? 'Personal');
    }

    public function switchAccount(string $accountId)
    {
        $account = Auth::user()
            ->emailAccounts()
            ->findOrFail($accountId);

        session(['selected_email_account_id' => $account->id]);

        return redirect()->route('mail.index', [
            'account' => $account->id,
            'mailbox' => 'inbox',
        ]);
    }

    public function updateIcon(string $icon): void
    {
        $this->authorizeSelectedAccount();

        $this->selectedAccount->update([
            'icon' => $icon,
        ]);

        $this->loadAccounts();
    }

    public function updateColor(string $color): void
    {
        $this->authorizeSelectedAccount();

        $this->selectedAccount->update([
            'color' => $color,
        ]);

        $this->loadAccounts();
    }

    public function saveNickname(): void
    {
        $this->authorizeSelectedAccount();

        $this->validate([
            'nickname' => ['nullable', 'string', 'max:50'],
        ]);

        $this->selectedAccount->update([
            'nickname' => $this->nickname,
        ]);

        $this->loadAccounts();
    }

    public function disconnect()
    {
        $this->authorizeSelectedAccount();

        $this->selectedAccount->delete();

        session()->forget('selected_email_account_id');

        $nextAccount = Auth::user()->emailAccounts()->first();

        if ($nextAccount) {
            session(['selected_email_account_id' => $nextAccount->id]);

            return redirect()->route('mail.index', [
                'account' => $nextAccount->id,
                'mailbox' => 'inbox',
            ]);
        }

        return redirect()->route('accounts.index');
    }

    private function authorizeSelectedAccount(): void
    {
        abort_unless(
            $this->selectedAccount &&
            $this->selectedAccount->user_id === Auth::id(),
            403
        );
    }

    public function render()
    {
        return view('livewire.account-switcher');
    }
}