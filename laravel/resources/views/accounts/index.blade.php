@extends('layouts.onboarding')

@section('content')
<div class="relative min-h-screen w-full overflow-hidden flex justify-center items-center text-fade-dusty-blue p-0">
    <div class="absolute inset-0 gradient-layer fade-aura pointer-events-none"></div>
    
    <div class="absolute top-0 left-0 p-5 z-20">
        @if (is_null(Auth::user()?->userPreference?->primary_email_account))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">
                    <img
                        src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                        alt="Fade logo"
                        class="h-10 w-auto"
                    />
                </button>
            </form>
        @else
            <a href="{{ route('dashboard') }}">
                <img
                    src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                    alt="Fade logo"
                    class="h-10 w-auto"
                />
            </a>
        @endif
    </div>

    <div class="flex flex-col justify-center items-center text-center relative z-10 w-full self-stretch space-y-6">
        <div class="space-y-8">
            <div>
                <p class="text-4xl">Connect another account</p>
                <p class="mt-4 text-xl">
                    Add another inbox to your Fade workspace.
                </p>
            </div>

            @include('partials.account-connect-options')

            <a href="{{ is_null(Auth::user()?->userPreference?->primary_email_account)
                ? route('home')
                : route('dashboard') }}">
                Back to Fade
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg p-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-fade-security-danger">
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif
    </div>
</div>


@endsection