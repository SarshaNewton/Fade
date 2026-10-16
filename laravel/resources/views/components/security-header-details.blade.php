@props([
    'headers' => [],
])

@php
    $auth = $headers['authentication-results'] ?? '';

    $checks = [
        'SPF' => str_contains(strtolower($auth), 'spf=pass') ? 'pass' : (str_contains(strtolower($auth), 'spf=fail') ? 'fail' : 'unknown'),
        'DKIM' => str_contains(strtolower($auth), 'dkim=pass') ? 'pass' : (str_contains(strtolower($auth), 'dkim=fail') ? 'fail' : 'unknown'),
        'DMARC' => str_contains(strtolower($auth), 'dmarc=pass') ? 'pass' : (str_contains(strtolower($auth), 'dmarc=fail') ? 'fail' : 'unknown'),
    ];

    $statusClasses = [
        'pass' => 'bg-fade-security-info/20 text-fade-security-info',
        'fail' => 'bg-fade-security-danger/20 text-fade-security-danger',
        'unknown' => 'bg-fade-dark-gray/10 text-fade-text-secondary',
    ];

    $routingDetails = [
        'From' => $headers['from'] ?? null,
        'Reply-To' => $headers['reply-to'] ?? null,
        'Return-Path' => $headers['return-path'] ?? null,
        'Message ID' => $headers['message-id'] ?? null,
    ];
@endphp

<div class="mt-1">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-4 rounded-2xl bg-white/70 border border-fade-border/50 p-5">
        <div>
            <h3 class="font-semibold mb-3">Authentication Checks</h3>

            <div class="space-y-2">
                @foreach($checks as $name => $status)
                    <div class="flex items-center justify-between rounded-xl bg-white/70 px-4 py-3">
                        <span class="font-medium">{{ $name }}</span>

                        <span class="{{ $statusClasses[$status] }} rounded-full px-3 py-1 text-xs font-semibold uppercase">
                            {{ $status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="font-semibold mb-3">Routing Details</h3>

            <div class="space-y-2">
                @foreach($routingDetails as $label => $value)
                    <div class="rounded-xl bg-white/70 px-4 py-3">
                        <p class="text-xs font-semibold text-fade-text-secondary uppercase">
                            {{ $label }}
                        </p>

                        <p class="text-sm break-all">
                            {{ $value ?: 'Not available' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div> 
</div>