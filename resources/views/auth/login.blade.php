<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="bg-amber-100 border border-amber-400 text-amber-700 px-4 py-2 text-xs text-center font-mono">
            Din aktuelle IP registreret af systemet: {{ request()->ip() }}
        </div>

        {{-- 🟢 Dynamisk overskrift baseret på hvilken URL der besøges --}}
        <div class="mb-4 text-center">
            <h2 class="text-lg font-bold text-slate-800">
                @if(isset($roleTarget))
                    Log ind som <span class="text-indigo-600">{{ ucfirst($roleTarget) }}</span>
                @else
                    Log ind
                @endif
            </h2>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- 🟢 Send eventuelt target-rollen med som skjult felt, hvis du vil tjekke det i backend --}}
            @if(isset($roleTarget))
                <input type="hidden" name="role_target" value="{{ $roleTarget }}">
            @endif

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>