<div
    x-data="{ iconMenuOpen: false, editingNickname: false }"
>
    
   <form method="POST" action="{{ route('logout') }}" class="flex justify-end">
        @csrf

        <button
            type="submit"
            class="mb-4 flex justify-end items-center gap-2 text-sm font-semibold text-fade-secondary-accent"
        >
            <x-lucide-log-out class="h-4 w-4" />
            Log out
        </button>
    </form>

    @if($selectedAccount)
        {{-- Active account --}}
        <div class="flex flex-col items-center text-center pb-5 border-b border-fade-border/50">

            {{-- Icon --}}
            <button
                type="button"
                @click="iconMenuOpen = !iconMenuOpen"
                class="mb-3 flex h-12 w-12 items-center justify-center rounded-full text-white"
                style="background-color: {{ $selectedAccount->color ?? '#354E96'}};"
            >
                @include('partials.account-icon', ['icon' => $selectedAccount->icon ?? 'mail', 'class' => 'h-7 w-7'])
            </button>

            {{-- Icon + color picker --}}
            <div
                x-show="iconMenuOpen"
                @click.outside="iconMenuOpen = false"
                x-transition
                x-cloak
                class="mb-4 w-full rounded-2xl bg-white/80 border border-white/70 p-3 shadow"
            >
                <p class="mb-2 text-xs font-semibold text-fade-text-secondary">Choose icon</p>

                <div class="grid grid-cols-7 gap-2 mb-4">
                    @foreach($iconOptions as $icon)
                        <button
                            type="button"
                            wire:click="updateIcon('{{ $icon }}')"
                            class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-white"
                        >
                            @include('partials.account-icon', ['icon' => $icon, 'class' => 'h-5 w-5'])
                        </button>
                    @endforeach
                </div>

                <p class="mb-2 text-xs font-semibold text-fade-text-secondary">Choose color</p>

                <div class="flex justify-center gap-2">
                    @foreach($colorOptions as $color)
                        <button
                            type="button"
                            wire:click="updateColor('{{ $color }}')"
                            class="h-7 w-7 rounded-full border border-white shadow"
                            style = "background-color: {{ $color }};"
                        ></button>
                    @endforeach
                </div>
            </div>

            {{-- Nickname --}}
            <div class="w-full">
                <p
                    x-show="!editingNickname"
                    @dblclick="editingNickname = true; $nextTick(() => $refs.nicknameInput.focus())"
                    class="text-xl font-semibold text-fade-text-primary cursor-text"
                    title="Double-click to edit"
                >
                    {{ $selectedAccount->nickname ?? ucfirst($selectedAccount->provider ?? 'Personal') }}
                </p>

                <input
                    x-show="editingNickname"
                    x-ref="nicknameInput"
                    x-cloak
                    type="text"
                    wire:model.defer="nickname"
                    wire:keydown.enter="saveNickname"
                    @keydown.enter="editingNickname = false"
                    wire:blur="saveNickname"
                    @blur="editingNickname = false"
                    class="w-full bg-transparent text-xl font-semibold text-center border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-none"
                >
            </div>

            <p class="text-base text-fade-text-secondary">
                {{ $selectedAccount->email_address ?? $selectedAccount->email }}
            </p>
        </div>

        {{-- Other accounts --}}
        <div class="divide-y divide-fade-border/50">
            @foreach($accounts as $account)
                @continue($account->id === $selectedAccount->id)

                <button
                    type="button"
                    wire:click="switchAccount('{{ $account->id }}')"
                    class="w-full flex items-center gap-3 py-3 text-left hover:bg-white/60 rounded-xl px-2 transition"
                >
                    <div class="flex h-9 w-9 items-center justify-center rounded-full text-white"
                        style = "background-color: {{ $account->color ?? '#354E96' }}"
                    >
                        @include('partials.account-icon', ['icon' => $account->icon ?? 'mail', 'class' => 'h-5 w-5'])
                    </div>

                    <div>
                        <p class="text-lg font-semibold text-fade-text-primary">
                            {{ $account->nickname ?? ucfirst($account->provider) }}
                        </p>

                        <p class="text-sm text-fade-text-secondary">
                            {{ $account->email_address ?? $account->email }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>

        {{-- Connect another account --}}
        <a
            href="{{ route('accounts.index') }}"
            class="flex items-center gap-3 border-t border-fade-border/50 py-3 text-fade-text-primary hover:bg-white/60 rounded-xl px-2 transition"
        >
            <x-lucide-mail-plus class="h-6 w-6" />
            <span class="text-lg font-medium leading-tight">
                Connect another email account
            </span>
        </a>

        {{-- Disconnect --}}
        <button
            type="button"
            wire:click="disconnect"
            wire:confirm="Disconnect this email account?"
            class="w-full flex items-center gap-3 border-t border-fade-border/50 py-3 text-left text-fade-text-primary hover:bg-red-50 rounded-xl px-2 transition"
        >
            <x-lucide-unplug class="h-6 w-6" />
            <span class="text-lg font-medium">
                Disconnect this email account
            </span>
        </button>
    @else
        <p class="text-sm text-fade-text-secondary">No email account connected.</p>

        <a href="{{ route('accounts.index') }}" class="mt-3 block rounded-xl bg-fade-secondary-accent px-4 py-2 text-center text-white">
            Connect an account
        </a>
    @endif
</div>