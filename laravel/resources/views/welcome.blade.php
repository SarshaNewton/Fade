<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FADE - Secure AI Email Client</title>

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen overflow-x-hidden text-fade-text-primary">
    <main class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 gradient-layer fade-aura opacity-80 rounded-full blur-3xl"></div>

        <nav class="relative z-10 max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img
                    src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                    alt="Fade logo"
                    class="h-10 w-auto justify-self-center"
                >
               
            </div>

            <a href="{{ route('login') }}" class="text-sm text-fade-text-secondary hover:text-slate-950 transition">
                Log in
            </a>
        </nav>

        <section class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="mb-4">
                    <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-fade-text-primary">
                        Let the noise
                    </h1>

                    <img
                        src="{{ Vite::asset('resources/images/fade-logo-word-trans.svg') }}"
                        alt="Fade"
                        class="mt-4 h-28 md:h-36 w-auto fade-logo-motion"
                    >
                </div>

                <p class="text-lg md:text-xl text-fade-text-primary max-w-2xl mb-10 leading-relaxed">
                    FADE is a secure AI-enhanced email client that helps you understand,
                    prioritize, and protect your email communication without overwhelm.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a
                        href="{{ route('login') }}"
                        class="px-8 py-4 bg-fade-accent text-white hover:bg-fade-accent-hover rounded-2xl font-semibold transition text-center shadow-lg shadow-cyan-600/20"
                    >
                        Get Started
                    </a>

                    <a
                        href="#features"
                        class="px-8 py-4 bg-white/55 border border-white/80 hover:bg-white/75 rounded-2xl font-semibold transition text-center backdrop-blur text-slate-800"
                    >
                        Explore Features
                    </a>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-6 rounded-full bg-white/30 blur-3xl"></div>

                    {{-- Inbox card --}}
                    <div class="relative overflow-hidden rounded-3xl border border-white/80 bg-white/55 shadow-2xl shadow-slate-400/20 backdrop-blur-xl">
                        <div class="flex items-center justify-between border-b border-white/70 px-5 py-4">
                            <div>
                                <p class="text-lg text-fade-text-secondary"><i>Your New Inbox</i></p>
                            </div>

                            <button
                                class="relative flex h-10 w-36 cursor-default items-center rounded-full border border-white/60 bg-white/50 backdrop-blur-md shadow-lg shadow-cyan-200/20"
                                aria-label="Fade mode enabled"
                                disabled
                            >
                                {{-- Slider --}}
                                <span
                                    class="absolute inset-y-0 left-1/2 w-1/2 rounded-full bg-fade-mode shadow-[0_0_20px_rgba(59,130,246,0.25),inset_0_2px_8px_rgba(0,0,0,0.15)]"
                                ></span>

                                {{-- Classic --}}
                                <span class="relative z-10 flex w-1/2 justify-center">
                                    <img
                                        src="{{ Vite::asset('resources/images/icons/mode/classic-inactive.svg') }}"
                                        alt="Classic mode"
                                        class="h-10 w-10 opacity-70"
                                    >
                                </span>

                                {{-- Fade --}}
                                <span class="relative z-10 flex w-1/2 justify-center">
                                    <img
                                        src="{{ Vite::asset('resources/images/icons/mode/fade-active.svg') }}"
                                        alt="Fade mode"
                                        class="h-8 w-8"
                                    >
                                </span>
                            </button>
                        </div>

                        <div class="divide-y divide-blue-100/70">
                            {{-- High Priority --}}
                            <div class="grid grid-cols-[6px_auto_auto_1fr_auto] items-center gap-4 bg-blue-100/50">
                                <x-priority-marker :level="'high'" />

                                <x-lucide-star class="ml-5 h-5 w-5 text-fade-text-primary" stroke-width="1" />

                                <x-security-badge :level="'minimal'" :size="'h-5'" />

                                <div class="py-4">
                                    <p class="font-semibold text-fade-text-primary">
                                        Please review and sign your student agreement
                                    </p>

                                    <p class="mt-1 text-sm text-fade-text-secondary">
                                        Sign the student agreement in the application portal
                                    </p>
                                </div>

                                <div class="pr-5">
                                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs text-purple-700">
                                        Request
                                    </span>
                                </div>
                            </div>

                            {{-- Medium Priority --}}
                            <div class="grid grid-cols-[6px_auto_auto_1fr_auto] items-center gap-4 bg-white/60">
                                <x-priority-marker :level="'medium'" />

                                <x-lucide-star class="ml-5 h-5 w-5 text-fade-text-primary" stroke-width="1" />

                                <x-security-badge :level="'low'" :size="'h-5'" />

                                <div class="py-4">
                                    <p class="font-semibold text-fade-text-primary">
                                        Activation code provided below
                                    </p>

                                    <p class="mt-1 text-sm text-fade-text-secondary">
                                        No action needed
                                    </p>
                                </div>

                                <div class="pr-5">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">
                                        Info
                                    </span>
                                </div>
                            </div>

                            {{-- Low Priority --}}
                            <div class="grid grid-cols-[6px_auto_auto_1fr_auto] items-center gap-4 bg-white/40 opacity-70">
                                <x-priority-marker :level="'low'" />

                                <x-lucide-star class="ml-5 h-5 w-5 text-fade-text-primary" stroke-width="1" />

                                <x-security-badge :level="'minimal'" :size="'h-5'" />

                                <div class="py-4">
                                    <p class="font-semibold text-fade-text-primary">
                                        Newsletter update
                                    </p>

                                    <p class="mt-1 text-sm text-fade-text-secondary">
                                        Low priority. No action required.
                                    </p>
                                </div>

                                <div class="pr-5">
                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-700">
                                        Info
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating security card --}}
                    <div class="bg-warning-gradient absolute -bottom-20 left-40 right-2 rounded-3xl border border-white/80 bg-white/75 p-5 shadow-2xl shadow-slate-400/20 backdrop-blur-xl">
                        <div class="flex flex-col items-center gap-4 text-center">
                            <div>
                                <div class="flex items-center justify-center gap-2">
                                    <x-security-badge :level="'medium'" :size="'h-5'" />

                                    <h3 class="text-2xl font-bold text-fade-text-accent">
                                        Medium Risk
                                    </h3>
                                </div>

                                <p class="mt-2 text-sm text-fade-text-primary">
                                    Several indicators suggest this email should be reviewed carefully.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-white/70 p-4">
                                <p class="text-sm font-semibold text-fade-text-primary">Recommended Actions</p>
                                <p class="mt-1 text-xs text-fade-text-primary">
                                    Verify the sender before sharing personal information.
                                </p>
                            </div>

                            <div class="rounded-2xl bg-white/70 p-4">
                                <p class="text-sm font-semibold text-fade-text-primary">Top Findings</p>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-700">
                                        Urgent Language
                                    </span>
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-700">
                                        Credential Request
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="relative z-10 max-w-7xl mx-auto px-6 pb-24">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white/55 border border-white/75 p-6 rounded-3xl shadow-sm backdrop-blur">
                    <h3 class="text-lg font-semibold mb-3 text-fade-text-primary">AI Summaries</h3>
                    <p class=" text-sm leading-relaxed">
                        Quickly understand long emails through concise AI-generated summaries.
                    </p>
                </div>

                <div class="bg-white/55 border border-white/75 p-6 rounded-3xl shadow-sm backdrop-blur">
                    <h3 class="text-lg font-semibold mb-3">Security Analysis</h3>
                    <p class="text-sm leading-relaxed">
                        Detect suspicious patterns, risky links, sender issues and authentication problems.
                    </p>
                </div>

                <div class="bg-white/55 border border-white/75 p-6 rounded-3xl shadow-sm backdrop-blur">
                    <h3 class="text-lg font-semibold mb-3">Multi-Account Support</h3>
                    <p class="text-sm leading-relaxed">
                        Connect and manage multiple email accounts from one focused interface.
                    </p>
                </div>

                <div class="bg-white/55 border border-white/75 p-6 rounded-3xl shadow-sm backdrop-blur">
                    <h3 class="text-lg font-semibold mb-3">Fade Mode</h3>
                    <p class="text-sm leading-relaxed">
                        Surface urgent messages, actions and security insights while fading away clutter.
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>