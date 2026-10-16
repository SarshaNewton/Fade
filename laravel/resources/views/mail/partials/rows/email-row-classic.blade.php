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

    $subjectClass = $isUnread
        ? 'font-medium text-fade-text-primary'
        : 'font-normal text-fade-text-primary';

    $snippetClass = $isUnread
        ? 'text-fade-muted'
        : 'text-fade-text-secondary';

    $timeClass = $isUnread
        ? 'font-medium text-fade-text-primary'
        : 'text-fade-text-primary';

    $rowClass = $isUnread
        ? 'bg-white'
        : 'bg-white/45';

    $securityLevel = $email->securityScan?->risk;
    $attachment = $email->attachments?->first();
    $received = $email->received_at;

    $isDraftMailbox = ($activeMailboxRole ?? $mailbox->role ?? 'inbox') === 'draft';
    $openDraftUrl = route('compose.open', [
        'account' => $email->emailAccount,
        'draft' => $email,
    ]);
@endphp

<div
    class="
        group grid items-center gap-2
        border-b border-fade-border
        px-4 py-3 text-sm
        {{ $rowClass }}

        grid-cols-[0px_36px_minmax(0,1fr)_70px]
        hover:grid-cols-[28px_36px_minmax(0,1fr)_70px]

        xl:grid-cols-[0px_36px_36px_180px_280px_minmax(0,1fr)_40px_80px]
        xl:hover:grid-cols-[28px_36px_36px_180px_280px_minmax(0,1fr)_40px_80px]

        transition-all duration-150
    "
    :class="{
        'grid-cols-[28px_36px_minmax(0,1fr)_70px] xl:grid-cols-[28px_36px_36px_180px_280px_minmax(0,1fr)_40px_80px]': hasSelection || isSelected('{{ $email->id }}')
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
        {{-- Sender --}}
        <div class="min-w-0">
            <p 
                title="{{ $recipients->join(', ') }}"
                class="{{ $senderClass }} truncate text-sm"
            >
                {{ $personLabel }}
            </p>
        </div>

        {{-- Subject + mobile snippet --}}
        <div class="col-span-2 min-w-0 xl:col-span-1">
            <p class="{{ $subjectClass }} truncate text-sm">
                {{ $email->subject ?: '(No subject)' }}
            </p>

            <p class="{{ $snippetClass }} truncate text-sm xl:hidden">
                {{ html_entity_decode($email->body_snippet) ?: 'No snippet available' }}
            </p>
        </div>

        {{-- Desktop snippet --}}
        <div class="hidden min-w-0 xl:block">
            <p class="{{ $snippetClass }} truncate text-sm">
                {{ html_entity_decode($email->body_snippet) ?: 'No snippet available' }}
            </p>
        </div>

        {{-- Attachment --}}
        <div class="hidden text-fade-text-primary xl:block">
            @if($attachment)
                <x-lucide-paperclip
                    class="h-5 w-5"
                    stroke-width="1"
                />
            @endif
        </div>

        {{-- Time --}}
        <div class="min-w-0 text-right">
            <p class="{{ $timeClass }} truncate text-xs xl:text-sm">
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