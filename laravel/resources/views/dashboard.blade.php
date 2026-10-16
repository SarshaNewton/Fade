@php
    $avgRisk = $stats['averageRiskScore'] ?? 0;
@endphp

<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-fade-text-secondary">
                        Fade Admin
                    </p>

                    <h1 class="mt-2 text-4xl font-bold text-fade-dusty-blue">
                        Security Dashboard
                    </h1>

                    <p class="mt-2 text-sm text-fade-text-secondary">
                        Monitor suspicious emails, scan results, and workspace-level threats.
                    </p>
                </div>

               
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="rounded-3xl bg-white/35 backdrop-blur-xl border border-white/60 shadow-sm p-6">
                    <p class="text-sm text-fade-text-secondary">Total Threats</p>
                    <p class="mt-3 text-5xl font-light text-fade-text-priamry">
                        {{ $stats['totalThreats'] }}
                    </p>
                </div>

                <div class="rounded-3xl bg-white/35 backdrop-blur-xl border border-white/60 shadow-sm p-6">
                    <p class="text-sm text-fade-text-secondary">Threats This Week</p>
                    <p class="mt-3 text-4xl font-light text-fade-dusty-blue">
                        {{ $stats['weeklyThreats'] }}
                    </p>
                </div>

                <div class="rounded-3xl bg-white/35 backdrop-blur-xl border border-white/60 shadow-sm p-6">
                    <p class="text-sm text-fade-text-secondary">Average Risk Score</p>
                    <p
                        class="mt-3 text-4xl font-light
                        {{ $avgRisk >= config('security.spam_threshold')
                            ? 'text-fade-security-danger'
                            : 'text-fade-security-safe' }}"
                    >
                        {{ number_format($avgRisk, 1) }}
                    </p>
                </div>
            </div>

            <div class="rounded-[2rem] bg-white/35 backdrop-blur-xl border border-white/60 shadow-sm p-6 md:p-8">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-medium text-fade-text-primary">
                            Recent High-Risk Emails
                        </h2>
                        <p class="text-sm text-fade-text-secondary">
                            Findings are pulled from the security scan attached to each flagged email.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($threats as $threat)
                        @php
                            $email = $threat->email;
                            $scan = $threat->securityScan;
                            $findings = $scan?->security_reasons ?? [];

                            $severity = strtolower($threat->severity ?? 'high');

                            $severityClasses = match ($severity) {
                                'medium' => 'bg-orange-100/80 text-fade-security-warning',
                                'high', 'critical' => 'bg-red-100/80 text-fade-security-danger',
                                default => 'bg-green-100/80 text-fade-security-safe',
                            };

                            $severityBg = match ($severity) {
                                'medium' => 'bg-orange-100/40',
                                'high', 'critical' => 'bg-red-100/40',
                                default => 'bg-green-100/40',
                            };
                        @endphp
                        <a
                            href="{{ route('mail.index', [
                                'email' => $email?->id,
                                'detail' => 'security',
                                'mailbox' => 'spam',
                            ]) }}"
                            class="block rounded-3xl border border-white/40  p-5 {{$severityBg}} hover:scale-[1.01] transition"
                        >
                            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                                <div>
                                    <h3 class="mt-1 text-xl font-semibold text-fade-dusty-blue">
                                        {{ $email?->subject ?? 'No subject' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-fade-text-secondary">
                                        From: {{ $email?->from_email_address ?? 'Unknown sender' }}
                                    </p>
                                </div>

                                <div class="flex md:flex-col items-start md:items-end gap-2">
                                    <span class="px-3 py-1 rounded-full text-md font-semibold {{$severityClasses}}">
                                        {{ ucfirst($severity) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 rounded-2xl bg-white/80 p-4">
                                <p class="text-sm font-medium text-fade-text-primary">
                                    Security findings
                                </p>

                                <ul class="mt-3 space-y-2 text-sm text-fade-text-secondary">
                                    @forelse ($findings as $type => $messages)
                                        @foreach ((array) $messages as $message)
                                            <li class="flex gap-3">
                                                <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-fade-secondary-accent"></span>
                                                <span>
                                                    <strong class="text-fade-text-primary">
                                                        {{ \Illuminate\Support\Str::headline($type) }}:
                                                    </strong>
                                                    {{ $message }}
                                                </span>
                                            </li>
                                        @endforeach
                                    @empty
                                        <li>No findings available.</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div class="mt-4 flex justify-between text-sm text-fade-text-primary">
                                <span>Scan risk: {{ ucfirst($scan?->risk ?? 'unknown') }}</span>
                                <span>{{ optional($threat->detected_at)->format('d M Y, H:i') }}</span>
                            </div>
                        </a>
                     
                    @empty
                        <div class="rounded-3xl border border-dashed border-fade-dusty-blue/20 bg-white/60 p-8 text-center">
                            <p class="text-sm text-fade-text-secondary">
                                No threats detected yet.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>