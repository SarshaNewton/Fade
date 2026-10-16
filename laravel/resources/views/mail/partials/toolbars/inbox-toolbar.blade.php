@php
    $otherAccounts = collect($accounts)->filter(function ($account) use ($selectedAccount) {
        return $account->id !== $selectedAccount->id;
    });
@endphp
<div class="shrink-0 w-full flex items-center justify-between bg-white p-4 border-b border-fade-border">
    <div class="flex justify-start items-center gap-5 text-fade-text-primary">

        <form
            method="POST"
            action="{{ route('emails.bulk-action') }}"
            class="flex gap-6 items-center h-4 w-auto"
        >
            @csrf

            <template x-for="id in selectedEmails" :key="id">
                <input type="hidden" name="email_ids[]" :value="id">
            </template>

            {{-- Select All --}}
            <div class="relative group h-full">
                <button type="button" @click="toggleSelectAll()">
                    <img
                        :src="allVisibleSelected ? checked : unchecked"
                        alt="Select All"
                        class="h-full w-auto"
                    />
                </button>

                <span class="tooltip">
                    Select All
                </span>
            </div>

            {{-- Bulk Actions --}}
            <div
                x-show="hasSelection"
                x-transition
                x-cloak
                class="flex gap-4 h-full items-center border-r border-fade-text-primary pr-4"
            >
                <span class="text-xs text-fade-text-secondary whitespace-nowrap">
                    <span x-text="selectedCount"></span> selected
                </span>

                {{-- Delete --}}
                <div class="relative group h-full w-6">
                    <button type="submit" name="action" value="trash">
                        <x-lucide-trash class="h-6 w-6" stroke-width="1" />
                    </button>

                    <span class="tooltip">
                        Delete selected
                    </span>
                </div>

                {{-- Mark Read --}}
                <div class="relative group h-full">
                    <button type="submit" name="action" value="mark_read">
                        <x-lucide-eye
                            class="h-6 w-6"
                            stroke-width="1.25"
                        />
                    </button>

                    <span class="tooltip">
                        Mark as read
                    </span>
                </div>

                {{-- Mark Unread --}}
                <div class="relative group h-full">
                    <button type="submit" name="action" value="mark_unread">
                        <x-lucide-eye-off
                            class="h-6 w-6"
                            stroke-width="1.25"
                        />
                    </button>

                    <span class="tooltip">
                        Mark as unread
                    </span>
                </div>

                {{-- Star --}}
                <div class="relative group h-full">
                    <button type="submit" name="action" value="star">
                        <x-lucide-star
                            class="h-6 w-6"
                            stroke-width="1.25"
                        />
                    </button>

                    <span class="tooltip">
                        Star selected
                    </span>
                </div>

                {{-- Unstar --}}
                <div class="relative group h-full">
                    <button type="submit" name="action" value="unstar">
                        <x-lucide-star-off
                            class="h-6 w-6"
                            stroke-width="1.25"
                        />
                    </button>

                    <span class="tooltip">
                        Unstar selected
                    </span>
                </div>

                {{-- Clear --}}
                <div class="relative group h-full">
                    <button type="button" @click="clearSelection()">
                        <x-lucide-x
                            class="h-6 w-6"
                            stroke-width="1.25"
                        />
                    </button>

                    <span class="tooltip">
                        Clear selection
                    </span>
                </div>
            </div>
        </form>

        {{-- Sync --}}
        <span class="flex items-center gap-2">
            <form method="POST" action="{{ route('mail.sync', $selectedAccount) }}" class="h-5 w-5">
                @csrf

                <div class="relative group h-full w-full">
                    <button type="submit">
                        <x-lucide-cloud-sync
                            class="h-full w-full"
                            stroke-width="1"
                        />
                    </button>

                    <span class="tooltip">
                        Sync from {{ $selectedAccount->provider }}
                    </span>
                </div>
            </form>

           @if($selectedAccount?->last_synced_at)
                <p class="text-sm">
                    Last synced:
                    {{ $selectedAccount->last_synced_at->timezone('Europe/Bucharest')->diffForHumans() }}                    
                </p>
                @else
                <p class="text-sm sync-nudge px-2 rounded-2xl opacity-55">
                    ← Sync emails here!                
                </p> 
            @endif
        </span>
    </div>

    <x-account-pill-switcher
        :selected-account="$selectedAccount"
        :other-accounts="$otherAccounts"
    />
</div>