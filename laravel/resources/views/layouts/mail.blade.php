@php
    $fadeMode = session('fade_mode', false);    
    $inactiveNavClass = 'text-fade-text-primary hover:bg-white/70 transition';
    $activeNavClass = 'mode-active-nav font-semibold transition-colors duration-500 ease-in-out';
    $activeMailbox = request('mailbox', 'inbox');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('FADE - Mailbox') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased  {{ $fadeMode ? 'is-fade-mode' : 'is-classic-mode' }}">
    <div class="relative min-h-screen overflow-hidden gradient-shell">
        
        <div id="classicGradientLayer"  class="gradient-layer fade-classic-gradient transition-opacity duration-700 ease-in-out {{ $fadeMode ? 'opacity-0' : 'opacity-100' }}"></div>
        <div id="fadeGradientLayer"  class="gradient-layer fade-aura transition-opacity duration-700 ease-in-out {{ $fadeMode ? 'opacity-100' : 'opacity-0' }}"></div>

        <div class="relative z-10">
            <div class="flex min-h-[calc(100vh-4rem)]">
                <aside class="w-52 z-40 min-h-screen flex flex-col py-4
                        bg-white/20 backdrop-blur-2xl
                        border-r border-white/50
                        shadow-[8px_0_30px_rgba(14,41,88,0.08)]
                    ">
                    @isset($selectedAccount)
                        <div class="px-4">
                            <a href="{{ route('dashboard')}}">
                                <img
                                    src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                                    alt="Fade logo"
                                    class="mb-4 h-28 w-auto justify-self-center"
                                >
                            </a>
                    
                            <button
                                type="button"
                                onclick="openComposeModal()"
                                class="w-full justify-self-center flex items-center justify-center gap-2 rounded-xl bg-fade-secondary-accent px-4 py-2 text-white text-l"
                            >
                                <x-lucide-mail-plus class="h-5 w-5" />
                                Compose
                            </button>
                        </div>
                        
                        <div class="flex-1 flex items-center">
                            <nav class="flex flex-col gap-2 w-full">
                                <a
                                    href="{{ route('mail.index', ['account' => $selectedAccount->id, 'mailbox' => 'inbox']) }}"
                                    class=" w-5/6 flex items-center gap-2 rounded-r-full px-4 py-2 {{ $activeMailbox === 'inbox' ? $activeNavClass : $inactiveNavClass }}"
                                >
                                    <x-lucide-inbox class="h-5 w-5" />
                                    Inbox
                                </a>

                                <a
                                    href="{{ route('mail.index', ['account' => $selectedAccount->id, 'mailbox' => 'draft']) }}"
                                    class="w-5/6 flex items-center gap-2 rounded-r-full px-4 py-2 {{ $activeMailbox === 'draft' ? $activeNavClass : $inactiveNavClass }}"
                                >
                                    <x-lucide-notepad-text class="h-5 w-5" />
                                    Drafts
                                </a>

                                <a
                                    href="{{ route('mail.index', ['account' => $selectedAccount->id, 'mailbox' => 'sent']) }}"
                                    class="w-5/6 flex items-center gap-2 rounded-r-full px-4 py-2 {{ $activeMailbox === 'sent' ? $activeNavClass : $inactiveNavClass }}"
                                >
                                    <x-lucide-send class="h-5 w-5" />
                                    Sent
                                </a>

                                <div x-data="{ open: false }">
                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class = "w-5/6 flex items-center gap-2 rounded-r-full px-4 py-2 text-fade-text-secondary"
                                    >
                                        <x-lucide-chevron-down
                                            class="h-5 w-5 transition-transform duration-300"
                                            x-bind:class="open ? 'rotate-180' : ''"
                                        />
                                        <span>More</span>
                                    </button>

                                    <div
                                        x-show="open"
                                        x-transition
                                        class="flex flex-col gap-2"
                                        x-cloak
                                    >
                                        <a href="{{ route('mail.index', ['account' => $selectedAccount->id, 'mailbox' => 'outbox']) }}"
                                        class="w-5/6 flex items-center gap-2 rounded-r-full px-8 py-2 text-fade-text-secondary {{ $activeMailbox === 'outbox' ? $activeNavClass : $inactiveNavClass }}">
                                            <x-lucide-send-horizontal class="h-5 w-5" />
                                            Outbox
                                        </a>

                                        <a href="{{ route('mail.index', ['account' => $selectedAccount->id, 'mailbox' => 'spam']) }}"
                                        class="w-5/6 flex items-center gap-2 rounded-r-full px-8 py-2 text-fade-text-secondary {{ $activeMailbox === 'spam' ? $activeNavClass : $inactiveNavClass }}">
                                            <img
                                                src="{{ Vite::asset('resources/images/icons/security/security-critical-outline.svg') }}"
                                                alt="Fade logo"
                                                class="h-5 w-auto"
                                            />
                                            Spam
                                        </a>
                                    </div>
                                </div>
                            </nav>
                        </div>

                       <div 
                            x-data="{ fadeSettingsOpen: false, accountMenuOpen: false }"
                            class="relative flex flex-col items-center justify-center gap-2 text-fade-text-primary"
                        >
                            {{-- Fade Settings --}}
                            <button
                                type="button"
                                @click="fadeSettingsOpen = !fadeSettingsOpen; accountMenuOpen = false"
                                class="w-full p-2 flex items-center justify-center gap-2 hover:bg-white/70 transition"
                            >
                                <x-lucide-settings-2 class="h-5 w-5" stroke-width="1.5" />
                                <p>Fade Settings</p>
                            </button>

                            {{-- Account Menu --}}
                            <button
                                type="button"
                                @click="accountMenuOpen = !accountMenuOpen; fadeSettingsOpen = false"
                                class="rounded-full"
                            >
                                <x-lucide-user class="h-16 w-16" stroke-width="0.5" />
                            </button>

                            {{-- Fade Settings Popup --}}
                            <div
                                x-show="fadeSettingsOpen"
                                @click.outside="fadeSettingsOpen = false"
                                x-transition
                                x-cloak
                                class="absolute bottom-24 left-4 z-50 w-80 rounded-[2rem] bg-white/80 backdrop-blur-2xl border border-white/70 shadow-xl p-5"
                            >
                                Fade settings content goes here.
                            </div>

                            {{-- Account Switcher Popup --}}
                            <div
                                x-show="accountMenuOpen"
                                @click.outside="accountMenuOpen = false"
                                x-transition
                                x-cloak
                                class="fixed bottom-24 left-5 z-[9999] w-80 rounded-[2rem] bg-white/80 backdrop-blur-2xl border border-white/70 shadow-xl p-5"
                            >
                                <livewire:account-switcher 
                                    :selected-account="$selectedAccount"
                                    :key="'account-switcher-' . $selectedAccount->id"
                                />
                            </div>
                        </div>
                    @endisset
                </aside>

                <main class="flex flex-col flex-1 p-6 h-screen">
                    @if (session('sync_started'))
                        <div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 6000)"
                            class="mb-4 rounded-2xl border border-white bg-white/70 backdrop-blur-xl px-5 py-4 text-sm text-fade-text-primary shadow-sm"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold">Sync started</p>
                                    <p class="mt-1 text-fade-text-primary">
                                        {{ session('sync_started') }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="show = false"
                                    class="text-fade-text-primary hover:text-fade-dusty-blue"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class ="flex items-center justify-between">
                        <form action="" method="GET" class="flex-1">
                            <div class="flex items-center gap-3 bg-white/25 border-y border-r backdrop-blur-sm border-white/50 rounded-full px-4 py-1 w-3/4 h-8">
                                <x-lucide-search
                                    class="h-5 w-5 text-fade-text-secondary"
                                    stroke-width="1.75"
                                />

                                <input
                                    type="text"
                                    name="q"
                                    placeholder="Search mail"
                                    class="w-full bg-transparent border-none outline-none focus:ring-0 text-fade-text-primary placeholder:text-fade-text-secondary"
                                />
                            </div>
                        </form>

                        <button
                            id="fadeModeToggle"
                            class="relative flex h-10 w-36 items-center rounded-full bg-white/50 backdrop-blur-md border-y border-r border-white/50 transition"
                            aria-label="Toggle Fade mode"
                        >
                            {{-- Sliding active background --}}
                            <span
                                id="fadeModeSlider"
                                class="
                                    absolute inset-y-0 w-1/2 shadow-[inset_0_2px_8px_rgba(0,0,0,0.15)] rounded-full transition-all duration-500 ease-out
                                    {{ $fadeMode
                                        ? 'left-1/2 bg-fade-mode'
                                        : 'left-0 bg-classic-mode'
                                    }}
                                "
                            ></span>

                            {{-- Classic icon --}}
                            <span class="relative z-10 flex w-1/2 justify-center">
                                <img
                                    id="classicModeIcon"
                                    src="{{ Vite::asset($fadeMode
                                        ? 'resources/images/icons/mode/classic-inactive.svg'
                                        : 'resources/images/icons/mode/classic-active.svg'
                                    ) }}"
                                    alt="Classic mode"
                                    class="h-10 w-10 transition-opacity duration-200"
                                >
                            </span>

                            {{-- Fade icon --}}
                            <span class="relative z-10 flex w-1/2 justify-center">
                                <img
                                    id="fadeModeIcon"
                                    src="{{ Vite::asset($fadeMode
                                        ? 'resources/images/icons/mode/fade-active.svg'
                                        : 'resources/images/icons/mode/fade-inactive.svg'
                                    ) }}"
                                    alt="Fade mode"
                                    class="h-8 w-8 transition-opacity duration-200"
                                >
                            </span>
                        </button>
                    </div>

                    <div
                        id="mailCard"
                        class="bg-white/10 backdrop-blur-md rounded-3xl mt-5 overflow-hidden h-full min-h-0 relative"
                    >
                        <div
                            id="mailContentRegion"
                            class="h-full min-h-0 flex flex-col transition-all duration-300"
                        >
                            @yield('mail-content')
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <div
            id="composeModalOverlay"
            class="fixed bottom-6 right-6 z-50 hidden pointer-events-none"
        >
            <div
                id="composeModalContent"
                class="flex flex-col pointer-events-auto fixed bottom-6 right-6 h-[520px] w-[640px]
                        overflow-hidden rounded-2xl bg-white shadow-2xl transition-all duration-300 ease-in-out
                    ">
                @include('mail.partials.compose-modal', [
                    'account' => $selectedAccount,
                    'draft' => null,
                ])
            </div>
        </div>
    </div>

   <script>
    window.mailConfig = {
        isFadeMode: @json($fadeMode),
        classicActive: "{{ Vite::asset('resources/images/icons/mode/classic-active.svg') }}",
        classicInactive: "{{ Vite::asset('resources/images/icons/mode/classic-inactive.svg') }}",
        fadeActive: "{{ Vite::asset('resources/images/icons/mode/fade-active.svg') }}",
        fadeInactive: "{{ Vite::asset('resources/images/icons/mode/fade-inactive.svg') }}",
        fadeModeToggleUrl: "{{ route('fade-mode.toggle') }}",
        csrfToken: "{{ csrf_token() }}",
    };
</script>
    @livewireScripts
    @stack('scripts')
</body>
</html>