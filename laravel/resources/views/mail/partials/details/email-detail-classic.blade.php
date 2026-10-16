@php
    $securityLevel = $email->securityScan?->risk ?? 'unscanned';
    $aiInsight = $email->latestAiInsight;
    $recipients = $email->recipients;

    $toRecipients = $recipients
        ->where('type', 'to')
        ->map(fn ($recipient) => $recipient->name
            ? "{$recipient->name} <{$recipient->email_address}>"
            : $recipient->email_address
        )
        ->join(', ');

    if ($selectedEmail && $selectedEmail->is_unread) {
        $selectedEmail->update([
            'is_unread' => false,
        ]);
    }
@endphp
<div class="bg-white h-full min-h-0 overflow-y-auto fade-scrollbar p-6 flex-1 rtl-scroll">
    <div class="space-y-4 ltr-content">
        <div class="flex items-center gap-2">
            <h1 class="text-3xl font-semibold text-fade-text-accent">
                {{ $email->subject ?: '(No subject)' }}
            </h1>
            <x-security-badge :level="$securityLevel" :size="'h-8'"/>
        </div>
        
        <div class="text-sm flex flex-col gap-2">
            <div class="flex justify-between">
                <p class="text-fade-text-primary font-medium">
                    @if($email->from_name)
                        {{ $email->from_name }} &lt;{{ $email->from_email_address }}&gt;
                    @else
                        {{ $email->from_email_address }}
                    @endif
                </p>

                <div class="flex gap-2 text-fade-text-primary items-center">
                    <p>
                        {{ $email->received_at?->timezone('Europe/Bucharest')->format('M j, Y, H:i') }}
                    </p>

                    <div
                        x-data="emailActions({
                            starred: @js($email->is_starred),
                            starUrl: '{{ route('emails.star', $email) }}'
                        })"
                        class="flex items-center gap-2"
                    >
                        <button
                            type="button"
                            @click="toggleStar()"
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
                        
                        <button 
                            type="button"
                            onclick="openInlineCompose('{{ route('emails.reply', $email) }}')"
                            class="rounded-full p-1 hover:bg-slate-100"
                        >
                            <x-lucide-reply class="h-5 w-5" stroke-width="1" />
                        </button>

                        <div x-data="{ open: false }" class="relative m-0">
                            <button
                                type="button"
                                @click.stop="open = !open"
                                @click.outside="open = false"
                                class="rounded-full p-1 hover:bg-slate-100"
                                title="More"
                            >
                                <x-lucide-ellipsis-vertical
                                    class="h-5 w-5"
                                    stroke-width="1"
                                />
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                class="absolute right-0 top-8 z-50 w-44 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 text-sm shadow-lg"
                            >
                                <button
                                    type="button"
                                    onclick="openInlineCompose('{{ route('emails.forward', $email) }}')"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-left hover:bg-slate-50"
                                >
                                    <x-lucide-forward class="h-4 w-4" stroke-width="1" />
                                    <span>Forward</span>
                                </button>

                                <button
                                    type="button"
                                    onclick="openInlineCompose('{{ route('emails.replyAll', $email) }}')"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-left hover:bg-slate-50"
                                >
                                    <x-lucide-reply-all class="h-4 w-4" stroke-width="1" />
                                    <span>Reply all</span>
                                </button>

                                <div class="my-1 border-t border-slate-100"></div>

                                <form
                                    method="POST"
                                    action="{{ route('emails.trash', $email) }}"
                                    onsubmit="return confirm('Move this email to trash?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-left text-red-600 hover:bg-red-50"
                                    >
                                        <x-lucide-trash-2 class="h-4 w-4" stroke-width="1" />
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <p class="text-fade-text-secondary">
                    To:
                    <span>
                        {{ $toRecipients ?: 'No recipients' }}
                    </span>
                </p>
            </div>
        </div>
        

        <div class="prose max-w-none text-fade-text-primary pt-5 h-full">
            @if($email->body_html)
                <x-email-body-frame :email="$email" />
            @else
                <div class="prose max-w-none text-fade-text-primary pt-5 stretch-self">
                    {!! nl2br(e($email->body_text ?? $email->body_snippet ?? 'No content available.')) !!}
                </div>
            @endif
            <!-- Reply to / Foward -->
           <div id="inlineComposeContainer" class="hidden flex-col justify-center items-center ">
                <img
                    src="{{ Vite::asset('resources/images/decoration/zigzag.svg') }}"
                    alt=""
                    class="w-1/4 h-auto justify-self-center"
                >
                <div id="inlineComposeContent" class="mt-6 w-full p-4 border border-fade-light-border rounded-lg"></div>
           </div>
        </div>
    </div>
</div>
@include('mail.partials.toolbars.side-detail-toolbar')
