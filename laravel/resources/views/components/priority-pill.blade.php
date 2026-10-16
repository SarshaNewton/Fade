@props(['level' => 'none'])

@php
    $priority = [
        'high' => [
            'class' => 'bg-fade-priority-high',
            'icon' => 'lucide-chevrons-up',
            'label' => 'High Priority',
        ],
        'medium' => [
            'class' => 'bg-fade-priority-medium',
            'icon' => 'lucide-equal',
            'label' => 'Medium Priority',
        ],
        'low' => [
            'class' => 'bg-fade-priority-low',
            'icon' => 'lucide-chevrons-down',
            'label' => 'Low Priority',
        ],
        'none' => [
            'class' => 'bg-transparent',
            'icon' => null,
            'label' => 'No Priority',
        ],
    ];

    $item = $priority[$level] ?? $priority['none'];
@endphp

<div
    title="{{ $item['label'] }}"
    class="w-36 p-1 rounded-full flex items-center justify-center {{ $item['class'] }}"
>
    @if($item['icon'])
        <x-dynamic-component
            :component="$item['icon']"
            class="h-3.5 w-3.5 text-white"
            stroke-width="2"
        />
    @endif
    <p class="text-white">{{$item['label']}}<p>
</div>