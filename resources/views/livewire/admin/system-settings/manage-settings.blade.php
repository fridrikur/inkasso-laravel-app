{{-- 🟢 2-FAKTOR LOGIN INDSTILLINGER MED ROLLE-STYRING --}}
<div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-6">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                <span>🔐</span> 2-Faktor Login (2FA) & Rollekrav
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Styr om 2FA er aktivt, hvilken metode der skal bruges, og hvilke roller der er omfattet.
            </p>
        </div>
    </div>

    {{-- Global afbryder --}}
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-800 block">Aktiver 2-Faktor Login i systemet</span>
            <span class="text-[11px] text-slate-500">Global hovedafbryder for 2FA-sikkerhed.</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" wire:model.live="enable_2fa" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
        </label>
    </div>

    @if($enable_2fa)
        {{-- VALG AF PROVIDER --}}
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                Vælg 2FA Metodetype
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-3.5 rounded-xl border transition cursor-pointer {{ $two_factor_provider === 'totp' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 hover:bg-slate-50' }}">
                    <input 
                        type="radio" 
                        wire:model="two_factor_provider" 
                        value="totp" 
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <span>📱</span> Authenticator App (TOTP)
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Google Authenticator / Microsoft Authenticator app.
                        </div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3.5 rounded-xl border transition cursor-pointer {{ $two_factor_provider === 'twilio' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 hover:bg-slate-50' }}">
                    <input 
                        type="radio" 
                        wire:model="two_factor_provider" 
                        value="twilio" 
                        class="text-indigo-600 focus:ring-indigo-500"
                    />
                    <div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                            <span>💬</span> Twilio SMS
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Send verifikationskode direkte til mobil via SMS.
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- 🟢 STYRING AF 2FA KRAV PR. ROLLE --}}
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Kræv 2FA for følgende roller
                </label>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Aftrykmærkerne bestemmer, hvilke brugere der tvinges igennem 2FA ved login.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-1">
                @foreach($allRoles as $role)
                    <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">👤</span>
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">{{ $role->name }}</span>
                                <span class="text-[10px] text-slate-400 block">ID: {{ $role->id }}</span>
                            </div>
                        </div>

                        <input 
                            type="checkbox" 
                            wire:model="role_2fa.{{ $role->id }}" 
                            class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                        />
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</div>