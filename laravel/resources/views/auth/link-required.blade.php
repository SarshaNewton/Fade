<!-- resources/views/auth/link-required.blade.php -->
<div class="max-w-lg mx-auto p-6">
    <h1 class="text-xl font-semibold mb-2">Linking required</h1>
    <p class="text-gray-700 mb-4">
        This email looks like it belongs to an existing Fade account.
        Please log in to Fade first, then click below to link it.
    </p>

    @auth
        <form method="POST" action="{{ route('social.link.complete') }}">
            @csrf
            <button class="px-4 py-2 rounded bg-black text-white">Link this account</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="underline">Go to login</a>
    @endauth
</div>