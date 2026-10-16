@props([
    'icon' => 'fade',
    'class' => 'h-5 w-auto',
])

@switch($icon)
     @case('fade')
        <img
            src="{{ Vite::asset('resources/images/icons/utility/fade-shield-filled.svg') }}"
            alt="Fade"
            class="{{ $class }}"
        >
    @break

    @case('briefcase')
        <x-lucide-briefcase class="{{ $class }}" />
        @break

    @case('graduation-cap')
        <x-lucide-graduation-cap class="{{ $class }}" />
        @break

    @case('heart')
        <x-lucide-heart class="{{ $class }}" />
        @break

    @case('star')
        <x-lucide-star class="{{ $class }}" />
        @break

    @case('crown')
        <x-lucide-crown class="{{ $class }}" />
        @break

    @case('user')
        <x-lucide-user class="{{ $class }}" />
        @break

    @default
        <x-lucide-mail class="{{ $class }}" />
@endswitch