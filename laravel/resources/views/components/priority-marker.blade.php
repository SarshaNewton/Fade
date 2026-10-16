@props([
    'level' => 'none',
    'sticky' => false,
])

@php
    $priority = [
        'high' => [
            'class' => 'bg-fade-priority-high',
            'icon' => 'lucide-chevrons-up',
            'label' => 'High priority',
        ],
        'medium' => [
            'class' => 'bg-fade-priority-medium',
            'icon' => 'lucide-equal',
            'label' => 'Medium priority',
        ],
        'low' => [
            'class' => 'bg-fade-priority-low',
            'icon' => 'lucide-chevrons-down',
            'label' => 'Low priority',
        ],
        'none' => [
            'class' => 'bg-transparent',
            'icon' => null,
            'label' => 'No priority',
        ],
    ];

    $item = $priority[$level] ?? $priority['none'];
@endphp

<div
    title="{{ $item['label'] }}"
    class="self-stretch w-3 flex items-center justify-center {{ $item['class'] }}"
>
    @if($item['icon'])
        <div class="{{ $sticky ? 'sticky top-1/2 -translate-y-1/2' : 'h-full flex items-center justify-center' }}">
            <x-dynamic-component
                :component="$item['icon']"
                class="h-3.5 w-3.5 text-white"
                stroke-width="2"
            />
</div>
    @endif
</div>