@props(['level' => 'unscanned', 'size' => 'h-5'])
@php
    $risk = [
        'critical' => [
            'src' => 'resources/images/icons/security/security-critical-filled.svg',
            'label' => 'Critical risk',
            'message' => 'Critical risk',
        ],
        'high' => [
            'src' => 'resources/images/icons/security/security-critical-filled.svg',
            'label' => 'High risk',
            'message' => 'High risk',
        ],
        'medium' => [
            'src' => 'resources/images/icons/security/security-dangerous-filled.svg',
            'label' => 'Medium risk',
            'message' => 'Medium risk',
        ],
        'low' => [
            'src' => 'resources/images/icons/security/security-sus-filled.svg',
            'label' => 'Low risk',
            'message' => 'Low risk',
        ],
        'minimal' => [
            'src' => 'resources/images/icons/security/security-low-filled.svg',
            'label' => 'Minimal risk',
            'message' => 'Very low risk',
        ],
        'unscanned' => [
            'src' => 'resources/images/icons/security/security-unscanned-filled.svg',
            'label' => 'Unscanned risk',
            'message' => 'Security status unavailable',
        ],
    ];

    $item = $risk[$level] ?? $risk['unscanned'];
@endphp

 <div class="relative group overflow-visible">
    <img  src="{{ Vite::asset($item['src']) }}"
        alt="{{ $item['label'] }}"
        class="{{$size}} w-auto justify-self-center"
    />

    <span class="tooltip inbox justify-self-start">
        {{ $item['message'] }}
    </span>
</div>