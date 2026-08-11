<div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-4">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <span>📱</span> Twilio SMS & 2FA Indstillinger
        </h2>
        <p class="text-xs text-slate-500 mt-1">
            Administrer API-nøgler til SMS-udsendelse og to-faktor-godkendelse (Twilio Verify).
        </p>
    </div>

    <form wire:submit="save" class="space-y-4">
        
        {{-- ACCOUNT SID --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Account SID (starts with AC)
            </label>
            <input 
                type="text" 
                wire:model="twilio_sid" 
                placeholder="AC1c8e266..."
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden"
            >
            @error('twilio_sid') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- AUTH TOKEN --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Auth Token (Secret)
            </label>
            <input 
                type="password" 
                wire:model="twilio_token" 
                placeholder="••••••••••••••••••••••••••••••••"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden"
            >
            @error('twilio_token') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- VERIFY SERVICE SID --}}
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Verify Service SID (starts with VA)
            </label>
            <input 
                type="text" 
                wire:model="twilio_verify_sid" 
                placeholder="VAb5ef83b..."
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden"
            >
            @error('twilio_verify_sid') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end pt-2">
            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer"
            >
                <span>Gem Twilio-nøgler</span>
            </button>
        </div>
    </form>

    {{-- TESTFORBINDELSE SECTION --}}
    <div class="pt-6 border-t border-slate-100 space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Test Twilio integration</h3>
        
        <div class="flex gap-2">
            <input 
                type="text" 
                wire:model="test_phone" 
                placeholder="Indtast tlf.nr (fx 29609033)"
                class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-800 shadow-xs focus:outline-hidden"
            >
            <button 
                type="button" 
                wire:click="testConnection"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer shrink-0"
            >
                Send test-SMS
            </button>
        </div>
        @error('test_phone') <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p> @enderror
    </div>

</div>