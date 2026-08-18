<div class="min-h-screen flex items-center justify-center bg-slate-100 p-4">

    <div class="bg-white rounded-3xl shadow-xl p-8 w-full max-w-lg border border-slate-100 space-y-6">

        <div class="text-center space-y-2">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                {{ $providerType === 'twilio' ? '💬' : '📱' }}
            </div>

            <h1 class="text-xl font-bold text-slate-900">
                2-Faktor Login påkrævet
            </h1>

            <p class="text-xs text-slate-500 leading-relaxed">
                Din rolle kræver 2-faktor godkendelse for at tilgå systemet. Følg anvisningerne herunder for at fuldføre opsætningen.
            </p>
        </div>

        {{-- 🟢 TILFÆLDE 1: TOTP / AUTHENTICATOR APP OPSÆTNING --}}
        @if($providerType === 'totp')

            <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-200/80">
                <div class="p-3 bg-white rounded-xl shadow-xs border border-slate-200">
                    @if($user->two_factor_secret)
                        {!! $user->twoFactorQrCodeSvg() !!}
                    @else
                        <p class="text-xs text-amber-600 font-medium p-4 text-center">
                            Genererer 2FA nøgle... Hvis dette ikke ændrer sig, mangler 2FA at blive aktøret på brugeren.
                        </p>
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-3 text-center">
                    Scan QR-koden med din Authenticator-app (f.eks. Google Authenticator eller Microsoft Authenticator).
                </p>
            </div>

            <form wire:submit="confirm" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Indtast 6-cifret kode fra app
                    </label>

                    <input
                        type="text"
                        wire:model="code"
                        maxlength="6"
                        inputmode="numeric"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-lg tracking-widest font-mono outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        placeholder="123456"
                    >

                    @error('code')
                        <p class="text-xs text-rose-600 font-semibold mt-1.5">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                >
                    Aktiver 2-Faktor Login
                </button>
            </form>

        {{-- 🟢 TILFÆLDE 2: TWILIO SMS OPSÆTNING --}}
        @else

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Dit mobilnummer
                    </label>

                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="phone"
                            placeholder="+45 12345678"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                        >

                        <button
                            type="button"
                            wire:click="sendSmsCode"
                            class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition cursor-pointer shrink-0"
                        >
                            Send kode
                        </button>
                    </div>

                    @error('phone')
                        <p class="text-xs text-rose-600 font-semibold mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <form wire:submit="confirm" class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Indtast SMS-verifikationskode
                        </label>

                        <input
                            type="text"
                            wire:model="code"
                            maxlength="6"
                            inputmode="numeric"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-lg tracking-widest font-mono outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                            placeholder="123456"
                        >

                        @error('code')
                            <p class="text-xs text-rose-600 font-semibold mt-1.5">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                    >
                        Bekræft Mobil & Aktiver 2FA
                    </button>
                </form>
            </div>

        @endif

    </div>

</div>