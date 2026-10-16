<div >
    <div class="flex justify-center gap-20">
        <a
            href="{{ route('social.redirect', ['provider' => 'google']) }}"
            class="flex flex-col items-center gap-2 rounded-2xl px-6 py-4 hover:bg-white/20 transition"
        >
            <img
                src="{{ Vite::asset('resources/images/icons/providers/google-logo.svg') }}"
                alt="Google logo"
                class="h-32 w-auto"
            />
            <span class="text-sm font-medium">Google</span>
        </a>

        <a
            href="{{ route('social.redirect', ['provider' => 'microsoft']) }}"
            class="flex flex-col items-center gap-2 rounded-2xl px-6 py-4 hover:bg-white/20 transition"
        >
            <img
                src="{{ Vite::asset('resources/images/icons/providers/microsoft-logo.svg') }}"
                alt="Microsoft logo"
                class="h-32 w-auto"
            />
            <span class="text-sm font-medium">Microsoft</span>
        </a>
    </div>
</div>