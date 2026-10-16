<div class="relative min-h-screen w-full overflow-hidden bg-white flex justify-center items-center text-fade-dusty-blue p-0">

    <div
        id="stepOneGradient"
        class="text-center gradient-layer fade-aura transition-opacity duration-700 ease-in-out {{ $step === 0 ? 'opacity-100' : 'opacity-0' }}"
    ></div>

    @if($step > 0 && $step < 4)
        {{-- Left click zone: back --}}
        <button
            type="button"
            wire:click="back"
            class="absolute left-0 top-0 z-0 h-full w-1/2 cursor-w-resize"
            aria-label="Go back"
        ></button>

        {{-- Right click zone: next --}}
        <button
            type="button"
            wire:click="next"
            class="absolute right-0 top-0 z-0 h-full w-1/2 cursor-e-resize"
            aria-label="Continue"
        ></button>
    @endif

    <div class="absolute top-0 left-0 p-5 z-20">
        <a href = "{{route('home')}}">
            <img
                src="{{ Vite::asset('resources/images/fade-logo.svg') }}"
                alt="Fade logo"
                class="h-10 w-auto"
            />
        </a>
    </div>

    @if($step > 0)
        <p class="absolute top-10 right-10 z-20 text-sm text-gray-600">
            Step {{ $step }} of {{ $hasConnectedAccount ? 3 : 4 }}
        </p>
    @endif

    <div class="p-6 flex justify-center items-center text-center relative z-10 w-full self-stretch pointer-events-auto">
        @if ($step === 0)
            <div
                x-data="{ visible: true }"
                x-init="
                    setTimeout(() => visible = false, 1800);
                    setTimeout(() => $wire.next(), 2500);
                "
                x-bind:class="visible ? 'opacity-100' : 'opacity-0'"
                class="flex flex-col justify-center items-center h-full space-y-4 transition-opacity duration-700"
            >
                <p class="text-xl">Hey there...</p>
                <p class="text-4xl">Welcome to Fade</p>
            </div>
        @endif


        @if ($step === 1)
            <div class="space-y-3">
                <label class="block">
                    <span class="text-4xl">What should we call you?</span>
                    <input type="text" wire:model.defer="name" class="text-2xl text-center w-1/2 p-1 mt-7 border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-0">
                    @error('name') <p class="text-fade-security-dangerous text-sm mt-1">{{ $message }}</p> @enderror
                </label>

                <button wire:click="next" class="mt-6 px-4 py-2 rounded bg-fade-secondary-accent text-white">Next</button>
            </div>
        @endif

        @if ($step === 2)
            <div class="space-y-3">
                <label class="block">
                    <span class="text-4xl">Hi {{$name}}. Create your Fade username.</span>
                    <p class="text-xl mt-4">Your connected email accounts can be added or removed later.</p>
                    <input type="text" wire:model.defer="username" class="text-2xl text-center w-1/2 p-1 mt-7 border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-0">
                    @error('username') <p class="text-fade-security-dangerous text-sm mt-1">{{ $message }}</p> @enderror
                </label>

                <div class="mt-6 flex gap-2 justify-center">
                    <button wire:click="back" class="px-4 py-2 rounded border">Back</button>
                    <button wire:click="next" class="px-4 py-2 rounded bg-fade-secondary-accent text-white">Next</button>
                </div>
            </div>
        @endif

        @if ($step === 3)
            <div class="space-y-3">
                <span class="text-4xl">Nice choice. Let's secure your account.</span>
                <label class="mt-7 flex flex-col justify-center items-center">
                    <input type="password" wire:model.defer="password" class="w-1/2 text-2xl text-center rounded p-2 mt-1 border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-0">
                    @error('password') <p class="text-fade-security-dangerous text-sm mt-1">{{ $message }}</p> @enderror
                    <span class="text-sm">Create your password</span>
                </label>

                <label class="flex flex-col justify-center items-center">
                    <input type="password" wire:model.defer="password_confirmation" class="w-1/2 text-2xl text-center rounded p-2 mt-1 border-x-0 border-t-0 border-b border-dashed border-fade-dusty-blue focus:ring-0 focus:outline-0">
                    <span class="text-sm">Confirm your password</span>
                </label>

                <div class="mt-6 flex gap-2 justify-center items-center">
                    <button wire:click="back" class="px-4 py-2 rounded border">Back</button>
                    <button wire:click="finish" class="px-4 py-2 rounded bg-fade-secondary-accent text-white">Finish</button>
                </div>
            </div>
        @endif

       @if ($step === 4)
            <div class="relative w-full max-w-4xl mx-auto">

                <div class="absolute inset-0 gradient-layer fade-aura opacity-80 rounded-full blur-3xl"></div>

                <div class="relative z-10 space-y-8 text-center">
                    <div class="space-y-2">
                        <p class="text-4xl text-fade-dusty-blue">
                            One last step
                        </p>

                        <p class="text-lg text-fade-text-secondary">
                            Connect your first account to start using Fade.
                        </p>
                    </div>

                    @include('partials.account-connect-options')

                    <div class="flex justify-center">
                        <button
                            wire:click="back"
                            class="px-4 py-2 rounded border border-fade-dusty-blue/30"
                        >
                            Back
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

