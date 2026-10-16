@extends('layouts.mail')
@section('mail-content')

    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Sent</h1>
            <p class="text-sm text-gray-500">
                Messages sent from {{ $selectedAccount->email_address }}
            </p>
        </div>

        @if($emails->isEmpty())
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                No sent emails yet.
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border bg-white">
                @foreach($emails as $email)
                    <div class="border-b last:border-b-0 px-4 py-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">
                                    {{ $email->subject ?: '(No subject)' }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    To:
                                    @php
                                        $toRecipients = $email->recipients
                                            ->where('type', 'to')
                                            ->pluck('email_address')
                                            ->implode(', ');
                                    @endphp

                                    {{ $toRecipients ?: 'No visible recipients' }}
                                </p>

                                <p class="mt-2 line-clamp-2 text-sm text-gray-600">
                                    {{ $email->body_text ?: 'No message body' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-xs text-gray-400">
                                {{ optional($email->created_at)->format('M j, Y H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection