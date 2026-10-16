@extends('layouts.mail')
@section('mail-content')

    @if($selectedAccount)
        @php
            $draftMailbox = $selectedAccount->mailboxes->firstWhere('role', 'draft');
        @endphp

        @if($draftMailbox)
            <div class="mailbox">
                <h3 class="p-6">{{ $draftMailbox->name ?? 'Draft' }}</h3>

                @forelse($draftMailbox->emails as $email)
                    <button
                        type="button"
                        onclick="openDraftComposeModal('{{ route('compose.open', ['account' => $selectedAccount->id, 'draft' => $email->id]) }}')"
                        class="block w-full border-b p-4 text-left hover:bg-gray-50"
                    >
                        <strong>{{ $email->subject ?? '(No Subject)' }}</strong>

                        <div class="meta">
                            To:
                            {{ $email->to_email_address ?? 'No recipient yet' }}
                        </div>

                        <p>{{ $email->body_snippet ?? 'No preview available' }}</p>
                    </button>
                @empty
                    <p>No drafts yet.</p>
                @endforelse
            </div>
        @else
            <p>No drafts mailbox found.</p>
        @endif
    @else
        <p>No email account selected.</p>
    @endif
@endsection