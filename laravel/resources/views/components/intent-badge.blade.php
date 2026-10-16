@props(['intent' => 'other'])

@php
    $styles = [
        'informative' => 'bg-blue-100 text-blue-700',
        'requesting' => 'bg-purple-100 text-purple-700',
        'thankful' => 'bg-green-100 text-green-700',
        'apologetic' => 'bg-indigo-100 text-indigo-700',
        'complaining' => 'bg-red-100 text-red-700',
        'advisory' => 'bg-cyan-100 text-cyan-700',
        'congratulatory' => 'bg-emerald-100 text-emerald-700',
        'commanding' => 'bg-teal-100 text-teal-700',
        'promotional' => 'bg-gray-100 text-gray-700',
        'notification' => 'bg-sky-100 text-sky-700',
        'reminder' => 'bg-amber-100 text-amber-700',
        'confirmation' => 'bg-blue-100 text-blue-700',
        'warning' => 'bg-orange-100 text-orange-700',
        'invitation' => 'bg-lime-100 text-lime-700',
        'other' => 'bg-slate-100 text-slate-700',
    ];

    $labels = [
        'informative' => 'Info',
        'requesting' => 'Request',
        'thankful' => 'Thanks',
        'apologetic' => 'Apology',
        'complaining' => 'Complaint',
        'advisory' => 'Advice',
        'congratulatory' => 'Congrats',
        'commanding' => 'Command',
        'promotional' => 'Promo',
        'notification' => 'Notif',
        'reminder' => 'Reminder',
        'confirmation' => 'Confirm',
        'warning' => 'Warning',
        'invitation' => 'Invite',
        'other' => 'Other',
    ];

    $class = $styles[$intent] ?? $styles['other'];
    $label = $labels[$intent] ?? ucfirst($intent);
@endphp

<span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium {{ $class }}">
    {{ $label }}
</span>