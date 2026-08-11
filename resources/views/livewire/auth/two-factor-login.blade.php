<div class="min-h-screen flex items-center justify-center bg-slate-100 p-4">

    <div class="w-full max-w-md bg-white shadow-xl rounded-3xl p-8 border border-slate-100 space-y-6">

        <div class="text-center space-y-2">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                {{ $providerType === 'twilio' ? '💬' : '🔐' }}
            </div>
            
            <h1 class="text-xl font-bold text-slate-900">
                To-faktor Godkendelse
            </h1>

            <p class="text-xs text-slate-500 leading-relaxed">
                @if($providerType === 'twilio')
                    Indtast den 6-cifrede engangskode, vi har sendt til dit mobilnummer via SMS.
                @else
                    Indtast den 6-cifrede kode fra din godkendelses-app (f.eks. Google Authenticator).
                @endif
            </p>
        </div>

        <form wire:submit="verify" class="space-y-4">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Verifikationskode
                </label>
                <input
                    type="text"
                    wire:model="code"
                    maxlength="6"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-center text-lg tracking-widest font-mono outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                    placeholder="123456"
                    autofocus
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
                Bekræft og Log Ind
            </button>

        </form>

        {{-- Ekstra mulighed for genafsendelse ved SMS --}}
        @if($providerType === 'twilio')
            <div class="text-center pt-2 border-t border-slate-100">
                @if($smsResent)
                    <p class="text-xs text-emerald-600 font-semibold">
                        ✓ En ny SMS-kode er blevet afsendt!
                    </p>
                @else
                    <button 
                        type="button" 
                        wire:click="resendSms" 
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition cursor-pointer"
                    >
                        Modtog du ikke koden? Send ny SMS
                    </button>
                @endif
            </div>
        @endif

    </div>

</div>