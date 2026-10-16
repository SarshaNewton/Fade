@props([
    'severity' => 'info',
    'label' => 'Unknown finding',
    'description' => '',
    'messages' => [],
    'actions' => [],
    'count' => 0
])

@php
    $backgrounds = [
        'info' => 'bg-fade-security-info/20',
        'warning' => 'bg-fade-security-warning/20',
        'danger' => 'bg-fade-security-danger/20',
        'unknown' => 'bg-fade-dark-gray/10',
    ];

    $background = $backgrounds[$severity] ?? $backgrounds['unknown'];
@endphp

<div x-data="{open : false}" class="{{ $background }} rounded-xl p-3 transition-all duration-300 self-start min-w-[200px] max-w-full">
    <div class="flex items-center gap-3 min-h-[74px]">

        <button class="flex items-center"
            type="button"
            @click="open = !open"
        >
            <x-lucide-chevron-down 
                class="h-4 w-4 transition-transform duration-300"
                x-bind:class="open ? 'rotate-180' : ''"
                strokewidth="1"
            />       
        </button>

        <div>
            <p class="font-semibold">{{ $label }}</p>
            <p class="text-sm italic">{{ $count === 1 ? ($messages[0] ?? '') : $count . ' occurrences' }}</p>
        </div>
    </div>

    <div
        x-show="open"
        x-transition
        x-cloak
    >   
        @if($count > 1)
            <ul class="list-disc list-outside pl-5 space-y-1 text-sm mt-1">
            @foreach($messages as $message)
                <li>{{ $message }}</li>
            @endforeach
            </ul>
        @endif

        <div class="pt-4 px-0 w-full">
            <img
                src="{{ Vite::asset('resources/images/decoration/zigzag.svg') }}"
                alt=""
                class="w-1/2 h-auto justify-self-center"
            >

            <div class="px-0 pb-0 pt-4 flex flex-col text-sm">
                <p><span class="font-semibold">Explanation:</span> {{$description}}</p>
                <p class="font-semibold">Recommened Actions</p>
                <ul class="list-disc list-outside pl-5 space-y-1 text-sm mt-1">
                @foreach($actions as $action)
                    <li>{{ $action }}</li>
                @endforeach
                </ul>
            </div>
        </div>
        
    </div>
</div>