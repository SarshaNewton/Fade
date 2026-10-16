@props([
    'starred' => false,
])

<x-lucide-star
    {{ $attributes->class([
        'fill-current' => $starred,
        'h-5 w-5',
    ]) }}
    stroke-width="1"
/>