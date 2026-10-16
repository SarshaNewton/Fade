@extends('layouts.mail')

@section('mail-content')
    @php
        $activeDetail = request('detail', 'email');
        $activeMailboxRole = request('mailbox', 'inbox');

        $fadeMode = session('fade_mode', false);
        $isDetailView = filled(request('email'));

        $rowPartial = $fadeMode
            ? 'mail.partials.rows.email-row-fade'
            : 'mail.partials.rows.email-row-classic';

        $detailPartial = $fadeMode
            ? 'mail.partials.details.email-detail-fade'
            : 'mail.partials.details.email-detail-classic';

        $toolbarPartial = $isDetailView
            ? 'mail.partials.toolbars.detail-toolbar'
            : 'mail.partials.toolbars.inbox-toolbar';

        $loadingText = $fadeMode
            ? 'Exiting Fade Mode'
            : 'Entering Fade Mode';
    @endphp

    @if($isDetailView)
        @include($toolbarPartial, ['email' => $selectedEmail ?? null])
    @endif

    <div class="h-full min-h-0 overflow-hidden flex-1">
        @if($selectedAccount)
            @forelse($selectedAccount->mailboxes->where('role', $activeMailboxRole) as $mailbox)

                <div
                    id="mailboxRegion"
                    class="relative mailbox h-full min-h-0 overflow-hidden flex flex-col transition-all duration-500 ease-in-out"
                    @if(!$isDetailView)
                        x-data="{
                            selectedEmails: [],
                            visibleEmailIds: @js($mailbox->emails->pluck('id')->map(fn ($id) => (string) $id)->values()),

                            checked: '{{ Vite::asset('resources/images/icons/utility/checkbox-checked.svg') }}',
                            unchecked: '{{ Vite::asset('resources/images/icons/utility/checkbox-unchecked.svg') }}',

                            isSelected(id) {
                                return this.selectedEmails.includes(String(id));
                            },

                            toggleEmail(id) {
                                id = String(id);

                                if (this.isSelected(id)) {
                                    this.selectedEmails = this.selectedEmails.filter(emailId => emailId !== id);
                                } else {
                                    this.selectedEmails.push(id);
                                }
                            },

                            toggleSelectAll() {
                                if (this.allVisibleSelected) {
                                    this.selectedEmails = [];
                                } else {
                                    this.selectedEmails = [...this.visibleEmailIds];
                                }
                            },

                            clearSelection() {
                                this.selectedEmails = [];
                            },

                            get allVisibleSelected() {
                                return this.visibleEmailIds.length > 0
                                    && this.visibleEmailIds.every(id => this.selectedEmails.includes(String(id)));
                            },

                            get hasSelection() {
                                return this.selectedEmails.length > 0;
                            },

                            get selectedCount() {
                                return this.selectedEmails.length;
                            }
                        }"
                    @endif
                >
                    <div
                        id="mailboxLoadingOverlay"
                        class="hidden absolute inset-0 z-50 bg-white/60 backdrop-blur-sm items-center justify-center"
                    >
                        <div class="flex flex-col items-center gap-3 text-fade-text-primary">
                            <div class="h-8 w-8 rounded-full border-2 border-fade-text-secondary border-t-transparent animate-spin"></div>
                            <p class="text-sm font-medium">{{ $loadingText }}</p>
                        </div>
                    </div>

                    @if(!$isDetailView)
                        @include($toolbarPartial, [
                            'email' => null,
                            'mailbox' => $mailbox,
                            'activeMailboxRole' => $activeMailboxRole,
                        ])
                    @endif

                    <div class="flex-1 min-h-0 flex flex-col">
                        @if($selectedEmail)
                            <div class="h-full min-h-0 overflow-hidden flex">
                                @if($activeDetail === 'security')
                                    @include('mail.partials.details.email-security-detail', ['email' => $selectedEmail])
                                @else
                                    @include($detailPartial, ['email' => $selectedEmail])
                                @endif
                            </div>
                        @else
                            <div class="flex-1 min-h-0 overflow-y-auto overflow-x-auto fade-scrollbar ">
                                <div class="min-h-full min-w-[900px] xl:min-w-0 flex flex-col ">
                                    @forelse($mailbox->emails as $email)
                                        @include($rowPartial, [
                                            'email' => $email,
                                            'mailbox' => $mailbox,
                                            'activeMailboxRole' => $activeMailboxRole,
                                        ])
                                    @empty
                                        <div class="flex-1 min-h-[300px] bg-white flex items-center justify-center text-fade-text-secondary">
                                            No emails here yet!
                                        </div>
                                    @endforelse

                                    @if($mailbox->emails->isNotEmpty())
                                        <div class="flex-1 bg-white"></div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @empty
                <div class="h-full bg-white flex items-center justify-center text-fade-text-secondary">
                    No emails here yet!
                </div>
            @endforelse
        @else
            <p class="p-4">No email account selected.</p>
        @endif
    </div>
@endsection