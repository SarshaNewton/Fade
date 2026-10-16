@php
    $composeTitle = $draft
        ? ($draft->subject ?: 'No Subject')
        : 'New Message';

    $toRecipients = $draft?->recipients?->where('type', 'to')
        ->pluck('email_address')
        ->values()
        ->all()
        ?? ($toRecipients ?? []);

    $ccRecipients = $draft?->recipients?->where('type', 'cc')
        ->pluck('email_address')
        ->values()
        ->all()
        ?? [];

    $bccRecipients = $draft?->recipients?->where('type', 'bcc')
        ->pluck('email_address')
        ->values()
        ->all()
        ?? [];

    $subjectValue = $draft?->subject ?? ($subject ?? '');
    $bodyValue = $draft?->body_text ?? ($body ?? '');
    $bodyHtmlValue = $draft?->body_html ?? ($bodyHtml ?? null);

    $emailPill = 'inline-flex items-center gap-1 rounded-full email-pill border border-white/50 shadow-sm px-2.5 py-1 text-sm'
    
@endphp

@if(!($isInline ?? false))
<div id="composeHeader" onclick="expandComposeIfMinimized()" class="w-full menu flex justify-between items-center text-sm py-2 px-3">
    
    <p class="font-bold">{{ $composeTitle }}</p>

    <div class="flex items-center gap-2">
        <button  id="minimizeComposeBtn" type="button" onclick="event.stopPropagation(); minimizeComposeModal()">
            <x-lucide-minus class="h-5 w-5"
                stroke-width="1"/>
        </button>    
        
        <button
            id="maximizeComposeBtn"
            type="button"
            onclick="toggleMaximizeComposeModal()"
        >
            <x-lucide-maximize-2
                id="maximizeIcon"
                class="h-4 w-4"
                stroke-width="1"
            />

            <x-lucide-minimize-2
                id="restoreIcon"
                class="hidden h-4 w-4"
                stroke-width="1"
            />
        </button>
            
        <button onclick="closeComposeModal()">
            <x-lucide-x class="h-5 w-5"
                stroke-width="1"/>
        </button> 
    </div>
</div>
@endif
<div id="composeBody" class="h-full">
    <form
        id="composeForm"
        method="POST"
        action="{{ route('compose.save', ['account' => $account->id]) }}"
        x-data="composeRecipients({
            to: @js($toRecipients),
            cc: @js($ccRecipients),
            bcc: @js($bccRecipients),
        })"
        class="flex h-full flex-col bg-white text-fade-text-primary"
    >
        @csrf

        <input type="hidden" name="draft_id" value="{{ $draft->id ?? '' }}">


        <div  @click="recipientsExpanded = true" @click.outside="collapseRecipients()" class="border-b border-fade-light-border">
            {{-- TO ROW --}}
            <div class="flex min-h-9 items-center px-4 py-1 text-sm"">
                <span class="w-10 shrink-0 text-fade-text-secondary">To</span>

                <div class="flex flex-1 flex-wrap items-center gap-1" @click="$refs.toInput.focus()">
                    <template x-for="(email, index) in to" :key="'to-' + index">
                        <span class="{{ $emailPill }}">
                            <span
                                contenteditable="true"
                                class="outline-none"
                                x-text="email"
                                @input="to[index] = $event.target.innerText.trim()"
                            ></span>

                            <button type="button" @click.stop="to.splice(index, 1)" class="text-slate-500">
                                ×
                            </button>

                            <input type="hidden" name="to[]" :value="email" class="border-none"/>
                        </span>
                    </template>

                    <span
                        x-show="hiddenNormalCount() > 0"
                        class="text-sm text-fade-text-secondary"
                        x-text="`+ ${hiddenNormalCount()} ${hiddenNormalCount() === 1 ? 'other' : 'others'}`"
                    ></span>

                    <span
                        x-show="hiddenBccCount() > 0"
                        class="text-sm text-fade-text-secondary"
                        x-text="`+ ${hiddenBccCount()} Bcc`"
                    ></span>

                    <input
                        x-ref="toInput"
                        x-model="toInput"
                        type="text"
                        class="min-w-32 flex-1 bg-transparent px-1 py-1 outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent"
                        @keydown.enter.prevent="addRecipient('to')"
                        @keydown.tab="addRecipient('to')"
                        @keydown.,.prevent="addRecipient('to')"
                        @blur="addRecipient('to')"
                    >
                </div>

                <div class="ml-3 flex gap-3 text-fade-text-secondary">
                    <button
                        type="button"
                        x-show="!showCc"
                        @click.stop="openCc()"
                    >
                        Cc
                    </button>

                    <button
                        type="button"
                        x-show="!showBcc"
                        @click.stop="openBcc()"
                    >
                        Bcc
                    </button>
                </div>
            </div>

            {{-- CC ROW --}}
            <div
                x-show="showCc && recipientsExpanded"
                x-transition
                class="flex min-h-9 items-center px-4 py-1 text-sm"
            >
                <span class="w-10 shrink-0 text-fade-text-secondary">Cc</span>

                <div class="flex flex-1 flex-wrap items-center gap-1" @click="$refs.ccInput.focus()">
                    <template x-for="(email, index) in cc" :key="'cc-' + index">
                        <span class="{{ $emailPill }}">
                            <span
                                contenteditable="true"
                                class="outline-none"
                                x-text="email"
                                @input="cc[index] = $event.target.innerText.trim()"
                            ></span>

                            <button type="button" @click.stop="cc.splice(index, 1)" class="text-slate-500">
                                ×
                            </button>

                            <input type="hidden" name="cc[]" :value="email">
                        </span>
                    </template>

                    <input
                        x-ref="ccInput"
                        x-model="ccInput"
                        type="text"
                        class="min-w-32 flex-1 bg-transparent px-1 py-1 outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent"
                        @keydown.enter.prevent="addRecipient('cc')"
                        @keydown.tab="addRecipient('cc')"
                        @keydown.,.prevent="addRecipient('cc')"
                        @blur="addRecipient('cc')"
                    >
                </div>
            </div>

            {{-- BCC ROW --}}
            <div
                x-show="showBcc && recipientsExpanded"
                x-transition
                class="flex min-h-9 items-center px-4 py-1 text-sm"
            >
                <span class="w-10 shrink-0 text-fade-text-secondary">Bcc</span>

                <div class="flex flex-1 flex-wrap items-center gap-1" @click="$refs.bccInput.focus()">
                    <template x-for="(email, index) in bcc" :key="'bcc-' + index">
                        <span class="{{ $emailPill }}">
                            <span
                                contenteditable="true"
                                class="outline-none"
                                x-text="email"
                                @input="bcc[index] = $event.target.innerText.trim()"
                            ></span>

                            <button type="button" @click.stop="bcc.splice(index, 1)" class="text-slate-500">
                                ×
                            </button>

                            <input type="hidden" name="bcc[]" :value="email">
                        </span>
                    </template>

                    <input
                        x-ref="bccInput"
                        x-model="bccInput"
                        type="text"
                        class="min-w-32 flex-1 bg-transparent px-1 py-1 outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent"
                        @keydown.enter.prevent="addRecipient('bcc')"
                        @keydown.tab="addRecipient('bcc')"
                        @keydown.,.prevent="addRecipient('bcc')"
                        @blur="addRecipient('bcc')"
                    >
                </div>
            </div>
        </div>

        {{-- SUBJECT --}}
        <input
            type="text"
            name="subject"
            value="{{ $subjectValue }}"
            placeholder="Subject"
            class="w-full border-x-0 border-t-0 border-b border-fade-light-border px-4 py-2 text-sm outline-none placeholder:text-fade-text-secondary focus:outline-none focus:ring-0 focus:border-b focus:border-fade-light-border"
        >

        {{-- BODY --}}
        <input type="hidden" name="body_html" id="bodyHtmlInput">
        <input type="hidden" name="body_text" id="bodyTextInput">

        <div
            id="composeEditor"
            contenteditable="true"
            class="flex-1 overflow-y-auto px-4 py-3 text-sm outline-none border-none focus:outline-none focus:ring-0 focus:border-transparent"
        >{!! $bodyHtmlValue ?? nl2br(e($bodyValue ?? '')) !!}</div>

        {{-- FOOTER --}}
        <div class="flex items-center gap-3 px-4 py-3">
            <button
                type="submit"
                formaction="{{ route('compose.send', ['account' => $account->id]) }}"
                class="rounded-lg bg-blue-500 px-4 py-2 text-sm text-white"
            >
                Send
            </button>

            <button type="button" class="text-fade-text-secondary">
                <x-lucide-paperclip class="h-5 w-5" />
            </button>

            <button
                type="submit"
                class="ml-auto rounded-lg border border-slate-300 px-4 py-2 text-sm"
            >
                Save Draft
            </button>
        </div>
    </form>
</div>