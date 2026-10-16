@props([
    'selectedAccount',
    'otherAccounts' => collect(),
    'mailbox' => 'inbox',
])

<div class="flex items-center gap-2">
    <span class="text-sm text-fade-text-secondary">Viewing:</span>

    <span
        class="flex items-center gap-2 rounded-full px-3 py-1 text-sm email-toggler-active"
        style="background-color: {{ $selectedAccount->color ?? '#354E96' }}"
    >
         @include('partials.account-icon', [
            'icon' => $selectedAccount->icon ?? 'fade',
            'class' => 'h-3 w-3'
        ])
        {{ $selectedAccount->nickname ?? $selectedAccount->email_address }}
    </span>

    <div class="border-l pl-2 border-fade-text-primary pr-4 flex items-center gap-2">
        @foreach($otherAccounts as $account)
            <form method="POST" action="{{ route('accounts.switch', $account->id) }}">
                @csrf

                <button
                    type="submit"
                    class="flex items-center gap-2 rounded-full px-3 py-1 text-sm email-toggler-active hover:opacity-80 transition"
                    style="background-color: {{ $account->color ?? '#354E96' }}"
                >
                    @include('partials.account-icon', [
                        'icon' => $account->icon ?? 'fade',
                        'class' => 'h-3 w-3'
                    ])
                    {{ $account->nickname ?? $account->email_address }}
                </button>
            </form>
        @endforeach
    </div>
</div>