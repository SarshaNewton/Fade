@php
    $otherAccounts = collect($accounts)->filter(function ($account) use ($selectedAccount) {
        return $account->id !== $selectedAccount->id;
    });
@endphp
<div class="shrink-0 w-full flex items-center justify-between bg-white/25 p-4 border-x border-t backdrop-blur-sm border-white/50">
    <a
        href="{{ request()->fullUrlWithoutQuery('email') }}"
        class="inline-flex items-center gap-2 text-sm text-fade-text-secondary hover:text-fade-text-primary"
    >
        <x-lucide-arrow-left class="h-4 w-4" />
        Back to inbox
    </a>

     <x-account-pill-switcher
        :selected-account="$selectedAccount"
        :other-accounts="$otherAccounts"
    />
</div>