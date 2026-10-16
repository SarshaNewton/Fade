@php
    $isOutboundMailbox = in_array($activeMailboxRole ?? $mailbox->role ?? 'inbox', ['sent', 'draft', 'outbox'], true);

   $recipients = $email->recipients
        ->where('type', 'to')
        ->map(fn ($recipient) => $recipient->name ?: $recipient->email_address)
        ->filter()
        ->values();

    $recipientLabel = match (true) {
        $recipients->isEmpty() => 'No recipients',

        $recipients->count() === 1 => $recipients->first(),

        default => $recipients->first() . ' +' . ($recipients->count() - 1) . ' more',
    };

    $personLabel = $isOutboundMailbox
        ? $recipientLabel
        : ($email->from_name ?: $email->from_email_address ?: 'Unknown sender');
    
    $isUnread = $email->is_unread;

    $senderClass = $isUnread
        ? 'font-bold text-fade-text-primary'
        : 'font-normal text-fade-text-primary';

    $summaryClass = $isUnread
        ? 'font-bold text-fade-text-primary'
        : 'text-fade-text-primary';

    $timeClass = $isUnread
        ? 'font-medium text-fade-text-primary'
        : 'text-fade-text-primary';

    $securityLevel = $email->securityScan?->risk;
    $attachment = $email->attachments?->first();
    $received = $email->received_at;

    $aiInsight = $email->latestAiInsight;

    if ($aiInsight) {
        $actions = $aiInsight->action_needed;

        if (is_string($actions)) {
            $actions = json_decode($actions, true) ?: [];
        }

        $actions = collect($actions)
            ->filter(fn ($action) => filled($action) && $action !== '-')
            ->values();

        $actionText = match (true) {
            $actions->count() === 0 => 'No action needed',
            $actions->count() === 1 => $actions->first(),
            default => $actions->count() . ' actions needed',
        };

        $hasAction = $actions->isNotEmpty();

        $actionsColor = !$hasAction
            ? 'text-fade-muted'
            : ($isUnread ? 'text-fade-text-accent' : 'text-fade-text-primary');

        $actionsWeight = $hasAction && $isUnread
            ? 'font-medium'
            : 'font-normal';

        $actionsClass = "{$actionsWeight} {$actionsColor}";

        $priority = $aiInsight->urgency;

        $rowOpacity = match (true) {
            $priority === 'low' => 'opacity-75',
            $priority === 'medium' => 'opacity-85',
            $priority === 'high' => 'opacity-100',
            default => 'opacity-100',
        };
    }

    $rowBackground = $isUnread
        ? 'bg-white'
        : 'bg-white/45';

    $rowClass = isset($rowOpacity)
        ? "{$rowOpacity} {$rowBackground}"
        : $rowBackground;

    $isDraftMailbox = ($activeMailboxRole ?? $mailbox->role ?? 'inbox') === 'draft';
    $openDraftUrl = route('compose.open', [
        'account' => $email->emailAccount,
        'draft' => $email,
    ]);
@endphp

@if($aiInsight)
    <div
        class="
            group flex text-sm
            {{ $rowClass }}
            border-b border-fade-border
            min-h-[52px]
            transition-all duration-150
        "
    >
        <x-priority-marker :level="$aiInsight?->urgency ?? 'none'" />

        <div
            class="
                flex-1 min-w-0 px-4 py-3
                grid items-center gap-3

                grid-cols-[0px_32px_minmax(0,1fr)_72px]
                hover:grid-cols-[28px_32px_minmax(0,1fr)_72px]

                xl:grid-cols-[0px_32px_32px_minmax(280px,1.6fr)_16px_minmax(160px,.9fr)_minmax(110px,.5fr)_72px_24px_90px]
                xl:hover:grid-cols-[28px_32px_32px_minmax(280px,1.6fr)_16px_minmax(160px,.9fr)_minmax(110px,.5fr)_72px_24px_90px]

                transition-all duration-150
            "
            :class="{
                'grid-cols-[28px_32px_minmax(0,1fr)_72px] xl:grid-cols-[28px_32px_32px_minmax(280px,1.6fr)_16px_minmax(160px,.9fr)_minmax(110px,.5fr)_72px_24px_90px]': hasSelection || isSelected('{{ $email->id }}')
            }"
        >
            {{-- Select --}}
            <div class="row-span-3 flex items-center justify-center overflow-hidden xl:row-span-1">
                <button
                    type="button"
                    @click.stop.prevent="toggleEmail('{{ $email->id }}')"
                    class="
                        h-5 w-5 opacity-0 transition-opacity
                        group-hover:opacity-100
                    "
                    :class="{ 'opacity-100': hasSelection || isSelected('{{ $email->id }}') }"
                >
                    <img
                        :src="isSelected('{{ $email->id }}') ? checked : unchecked"
                        alt="Select"
                        class="h-full w-auto"
                    />
                </button>
            </div>

            {{-- Star --}}
            <div
                x-data="emailActions({
                    starred: @js($email->is_starred),
                    starUrl: '{{ route('emails.star', $email) }}'
                })"
                class="row-span-3 flex items-center justify-center text-fade-text-primary xl:row-span-1"
            >
                <button
                    type="button"
                    @click.stop.prevent="toggleStar()"
                    class="rounded-full p-1 hover:bg-slate-100"
                >
                    <template x-if="starred">
                        <x-lucide-star
                            class="h-5 w-5 fill-fade-text-primary text-fade-text-primary"
                            stroke-width="1"
                        />
                    </template>

                    <template x-if="!starred">
                        <x-lucide-star
                            class="h-5 w-5 text-fade-text-primary"
                            stroke-width="1"
                        />
                    </template>
                </button>
            </div>

            {{-- Security Badge --}}
            @if(! $isOutboundMailbox)
                <div class="hidden xl:flex items-center justify-center">
                    <a
                        href="{{ request()->fullUrlWithQuery([
                            'email' => $email->id,
                            'detail' => 'security',
                        ]) }}"
                        title="View security details"
                        class="inline-flex rounded-full"
                        @click.stop
                    >
                        <x-security-badge :level="$securityLevel ?? 'unscanned'" />
                    </a>
                </div>
            @else
                <div class="hidden xl:block"></div>
            @endif

            {{-- Clickable email content --}}
            @if($isDraftMailbox)
                <button
                    type="button"
                    class="contents text-left"
                    onclick="window.openDraftComposeModal('{{ $openDraftUrl }}')"
                >
            @else
                <a
                    href="{{ request()->fullUrlWithQuery([
                        'email' => $email->id,
                        'detail' => 'email',
                    ]) }}"
                    class="contents"
                >
            @endif

                {{-- Summary --}}
                <div class="min-w-0">
                    <p class="{{ $summaryClass }} text-sm line-clamp-2 leading-snug">
                        {{ $aiInsight->short_summary }}
                    </p>

                    {{-- Mobile sender/action preview --}}
                    <p class="mt-1 truncate text-xs text-fade-text-secondary xl:hidden">
                        {{ $email->from_name ?: $email->from_email_address ?: 'Unknown sender' }}
                        <span class="mx-1">•</span>
                        {{ $actionText }}
                    </p>
                </div>

                {{-- Divider --}}
                <div class="hidden justify-center xl:flex">
                    <span class="text-fade-text-secondary text-sm">
                        ⟡
                    </span>
                </div>

                {{-- Action --}}
                <div class="hidden min-w-0 xl:block">
                    <p class="{{ $actionsClass }} text-sm line-clamp-2 leading-snug">
                        {{ $actionText }}
                    </p>
                </div>

                {{-- Sender + Intent --}}
                <div class="hidden min-w-0 xl:block">
                    <p class="truncate text-sm {{ $senderClass }}">
                        {{ $email->from_name ?: $email->from_email_address ?: 'Unknown sender' }}
                    </p>
                </div>

                {{-- Intent --}}
                <div class="hidden justify-end xl:flex">
                    <x-intent-badge :intent="$aiInsight->intent" />
                </div>

                {{-- Attachment --}}
                <div class="hidden justify-center xl:flex">
                    @if($attachment)
                        <x-lucide-paperclip
                            class="h-4 w-4 text-fade-text-secondary"
                            stroke-width="1"
                        />
                    @endif
                </div>

                {{-- Time --}}
                <div class="justify-self-end">
                    <p class="{{ $timeClass }} whitespace-nowrap text-xs text-fade-text-secondary xl:text-sm">
                        {{ $received?->isAfter(now()->subDays(7))
                            ? $received->diffForHumans()
                            : $received?->format('M j')
                        }}
                    </p>
                </div>
            @if($isDraftMailbox)
                </button>
            @else
                </a>
            @endif
        </div>
    </div>
@else
    <div class="border-b border-fade-border bg-white/45 px-4 py-3 text-sm text-fade-text-secondary">
        No AI insight available yet.
    </div>
@endif