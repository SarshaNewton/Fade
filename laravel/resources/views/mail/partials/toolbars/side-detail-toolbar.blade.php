@php 
    $activeDetailClass = 'relative z-20 bg-white text-fade-text-primary shadow-[12px_10px_18px_-10px_rgba(14,41,88,0.18)] scale-y-[1.2] transition-all duration-500 ease-in-out';
    $inactiveDetailClass = 'relative z-10 bg-white/20 backdrop-blur-2xl border-y border-r border-white/50 text-fade-text-secondary hover:bg-white/40 scale-y-100 transition-all duration-500 ease-in-out';

    $activeIconClass = 'scale-y-[0.8333]';
    $inactiveIconClass = 'scale-y-100';
@endphp

<div class="flex flex-col w-10 shrink-0 overflow-visible">
    <a
        href="{{ request()->fullUrlWithQuery(['detail' => 'email']) }}"
        class="{{ $activeDetail === 'email' ? $activeDetailClass : $inactiveDetailClass }} flex-1 flex items-center justify-center rounded-br-2xl"
    >
        <x-lucide-mail-open
            class="h-6 w-6 {{ $activeDetail === 'email' ? $activeIconClass : $inactiveIconClass }}"
            stroke-width="1"
        />
    </a>

    <a
        href="{{ request()->fullUrlWithQuery(['detail' => 'security']) }}"
        class="{{ $activeDetail === 'security' ? $activeDetailClass : $inactiveDetailClass }} flex-1 flex items-center justify-center rounded-tr-2xl"
    >
        <img
            src="{{ Vite::asset('resources/images/icons/utility/fade-shield-outline.svg') }}"
            alt="Fade security shield"
            class="h-6 w-auto {{ $activeDetail === 'security' ? $activeIconClass : $inactiveIconClass }}"
        />
    </a>
</div>