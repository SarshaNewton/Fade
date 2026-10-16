<x-guest-layout>
    <div class="relative min-h-screen overflow-hidden bg-white flex items-center justify-center text-fade-dusty-blue">

        <div class="absolute inset-0 gradient-layer fade-aura"></div>

        <div class="absolute top-0 left-0 p-5 z-20">
            <a href ="{{ route('home') }}">
                <img
                    src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                    alt="Fade logo"
                    class="h-10 w-auto"
                />
            </a>
        </div>

        <div class="relative z-10 w-full max-w-xl text-center px-6">

            <h1 class="text-5xl font-light mb-3">
                Welcome back
            </h1>

            <p class="text-lg text-fade-text-secondary mb-12">
                Sign in to continue your journey with Fade.
            </p>

            <x-auth-session-status
                class="mb-6"
                :status="session('status')"
            />
            <div class="flex justify-center gap-4">
                <a
                    href="{{ route('social.redirect', ['provider' => 'google']) }}"
                    class="flex items-center gap-2 rounded-full px-6 py-4 bg-white/70 hover:bg-white/90 transition"
                >
                    <img
                        src="{{ Vite::asset('resources/images/icons/providers/google-logo.svg') }}"
                        alt="Google logo"
                        class="h-8 w-auto"
                    />
                    <span class="text-md font-medium">Google</span>
                </a>

                <a
                    href="{{ route('social.redirect', ['provider' => 'microsoft']) }}"
                    class="flex items-center gap-2 rounded-full px-6 py-4 bg-white/70 hover:bg-white/90 transition"
                >
                    <img
                        src="{{ Vite::asset('resources/images/icons/providers/microsoft-logo.svg') }}"
                        alt="Microsoft logo"
                        class="h-8 w-auto"
                    />
                    <span class="text-md font-medium">Microsoft</span>
                </a>
            </div>

            <div class="my-6 flex items-center gap-3">
                <div class="h-px flex-1 bg-white/30"></div>
                <span class="text-xs uppercase tracking-wide text-gray-400">or</span>
                <div class="h-px flex-1 bg-white/30"></div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        placeholder="Username"
                        class="w-full text-center text-xl bg-transparent border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-none"
                    />

                    <x-input-error
                        :messages="$errors->get('username')"
                        class="mt-2"
                    />
                </div>

                <div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        placeholder="Password"
                        class="w-full text-center text-xl bg-transparent border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-none"
                    />

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />
                </div>

                <div class="flex justify-center">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded border-white"
                        >
                        <span>Remember me</span>
                    </label>
                </div>

                <div class="flex justify-center">
                    <button
                        type="submit"
                        class="px-8 py-3 rounded-lg bg-fade-secondary-accent text-white hover:opacity-90 transition"
                    >
                        Log In
                    </button>
                </div>

                @if (Route::has('password.request'))
                    <div>
                        <a
                            href="{{ route('password.request') }}"
                            class="text-sm text-fade-text-secondary hover:underline"
                        >
                            Forgot your password?
                        </a>
                    </div>
                @endif

                <div class="pt-6 border-t border-white/50">
                    <p class="text-sm text-fade-text-secondary">
                        Not a member?
                    </p>

                    <a
                        href="{{ route('onboarding.show') }}"
                        class="text-fade-secondary-accent font-medium hover:underline"
                    >
                        Create your Fade account →
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>