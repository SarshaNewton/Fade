@props(['severity' => 'unknown', 'label' => 'Unknown'])

@php
    $background = [
        'info' => 'bg-fade-security-info',
        'warning' => 'bg-fade-security-warning',
        'danger' => 'bg-fade-security-danger',
        'unknown' => 'bg-fade-dark-gray'   
    ];
$backgroundClass = $background[$severity] ?? $background['unknown'];

@endphp

<div class="{{$backgroundClass}} px-2 py-1 w-fit text-center rounded-full">
    <p class="text-white">{{$label}}</p>
</div>