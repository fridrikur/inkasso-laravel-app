<div class="max-w-6xl mx-auto space-y-6 pb-12">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Systemindstillinger
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Tilpas systemets tema, sagsbehandlerfarver og sikkerhedsindstillinger
            </p>
        </div>

        <button 
            type="button" 
            wire:click="save" 
            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-2"
        >
            <span>💾</span> Gem Indstillinger
        </button>
    </div>

    {{-- 🎨 FARVETEMA SEKTION MED LIVE PREVIEW --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span>🎨</span> Farvetema & Live Sags-Eksempel
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Ændringer i farvevælgerne opdaterer øjeblikkeligt eksemplet på sagen til højre.
                </p>
            </div>
        </div>

        {{-- GRID MED INDSTILLINGER OG PREVIEW --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- VENSTRE KOLONNE: TEMA PRESETS OG FARVEVÆLGERE (7 Cols) --}}
            <div class="lg:col-span-7 space-y-5">

                {{-- PRESET KNAPPER --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Vælg Tema Preset
                    </label>

                    <div class="grid grid-cols-3 gap-2.5">
                        <button
                            type="button"
                            wire:click="setPreset('default')"
                            class="p-3 rounded-2xl border text-left transition cursor-pointer {{ $theme_preset === 'default' ? 'border-indigo-500 bg-indigo-50/40 ring-2 ring-indigo-500/10' : 'border-slate-200 hover:bg-slate-50' }}"
                        >
                            <div class="text-xs font-bold text-slate-900 flex items-center justify-between">
                                <span>✨ Standard</span>
                                @if($theme_preset === 'default') <span class="text-indigo-600 font-bold">✓</span> @endif
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Indigo & Slate</div>
                            <div class="flex gap-1 mt-2">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #4f46e5;"></span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #0f172a;"></span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #f1f5f9;"></span>
                            </div>
                        </button>

                        <button
                            type="button"
                            wire:click="setPreset('legacy')"
                            class="p-3 rounded-2xl border text-left transition cursor-pointer {{ $theme_preset === 'legacy' ? 'border-indigo-500 bg-indigo-50/40 ring-2 ring-indigo-500/10' : 'border-slate-200 hover:bg-slate-50' }}"
                        >
                            <div class="text-xs font-bold text-slate-900 flex items-center justify-between">
                                <span>🏛️ Legacy</span>
                                @if($theme_preset === 'legacy') <span class="text-indigo-600 font-bold">✓</span> @endif
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Klassisk DKG</div>
                            <div class="flex gap-1 mt-2">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #1e3a8a;"></span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #1e293b;"></span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300" style="background-color: #e2e8f0;"></span>
                            </div>
                        </button>

                        <button
                            type="button"
                            wire:click="$set('theme_preset', 'custom')"
                            class="p-3 rounded-2xl border text-left transition cursor-pointer {{ $theme_preset === 'custom' ? 'border-indigo-500 bg-indigo-50/40 ring-2 ring-indigo-500/10' : 'border-slate-200 hover:bg-slate-50' }}"
                        >
                            <div class="text-xs font-bold text-slate-900 flex items-center justify-between">
                                <span>🛠️ Custom</span>
                                @if($theme_preset === 'custom') <span class="text-indigo-600 font-bold">✓</span> @endif
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">Egne valgte farver</div>
                            <div class="text-[10px] text-indigo-600 font-semibold mt-2">Valgfri farvevalg</div>
                        </button>
                    </div>
                </div>

                {{-- FARVE PICKERS --}}
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Tilpas Farvekoder
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        {{-- Primær knapfarve --}}
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                            <label class="block text-[11px] font-bold text-slate-800">Primær / Handling (Gem Sag)</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="theme_primary" wire:change="$set('theme_preset', 'custom')" class="w-7 h-7 rounded-lg border border-slate-300 cursor-pointer p-0.5 bg-white shrink-0">
                                <input type="text" wire:model.blur="theme_primary" class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-mono uppercase bg-white">
                            </div>
                        </div>

                        {{-- Sidebar Baggrund --}}
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                            <label class="block text-[11px] font-bold text-slate-800">Sidebar Baggrund</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="theme_sidebar_bg" wire:change="$set('theme_preset', 'custom')" class="w-7 h-7 rounded-lg border border-slate-300 cursor-pointer p-0.5 bg-white shrink-0">
                                <input type="text" wire:model.blur="theme_sidebar_bg" class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-mono uppercase bg-white">
                            </div>
                        </div>

                        {{-- Sagsbehandler Header --}}
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                            <label class="block text-[11px] font-bold text-slate-800">Sag Header</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="theme_sag_editor_header" wire:change="$set('theme_preset', 'custom')" class="w-7 h-7 rounded-lg border border-slate-300 cursor-pointer p-0.5 bg-white shrink-0">
                                <input type="text" wire:model.blur="theme_sag_editor_header" class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-mono uppercase bg-white">
                            </div>
                        </div>

                        {{-- Sag Outer Wrapper --}}
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1">
                            <label class="block text-[11px] font-bold text-slate-800">Sag Ydre Ramme / Baggrund</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="theme_sag_editor_wrapper_bg" wire:change="$set('theme_preset', 'custom')" class="w-7 h-7 rounded-lg border border-slate-300 cursor-pointer p-0.5 bg-white shrink-0">
                                <input type="text" wire:model.blur="theme_sag_editor_wrapper_bg" class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-mono uppercase bg-white">
                            </div>
                        </div>

                        {{-- Sag Kort Baggrund --}}
                        <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1 sm:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-800">Sagsbehandler Kort / Indhold</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model.live="theme_sag_editor_bg" wire:change="$set('theme_preset', 'custom')" class="w-7 h-7 rounded-lg border border-slate-300 cursor-pointer p-0.5 bg-white shrink-0">
                                <input type="text" wire:model.blur="theme_sag_editor_bg" class="w-full rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-mono uppercase bg-white">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- HØJRE KOLONNE: LIVE PREVIEW KORT AF SAGEN (5 Cols) --}}
            <div class="lg:col-span-5 flex flex-col">
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center justify-between">
                    <span>👁️ Live Forhåndsvisning</span>
                    <span class="text-[10px] text-slate-400 font-normal">Sagsbehandler-visning</span>
                </div>

                {{-- MOCKUP MIKRO-SYSTEM --}}
                <div class="rounded-2xl border border-slate-300/80 overflow-hidden shadow-lg flex-1 flex flex-col bg-slate-900 text-xs select-none">
                    
                    {{-- MINI TOPBAR --}}
                    <div class="px-3 py-2 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                        <div class="flex items-center gap-1.5 font-bold text-[11px]">
                            <span>⚖️</span>
                            <span>{{ $app_name }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Sag #1042</span>
                    </div>

                    <div class="flex flex-1">
                        {{-- MINI SIDEBAR --}}
                        <div class="w-16 p-2 text-white/80 space-y-2 border-r border-slate-800 flex flex-col justify-between shrink-0" style="background-color: {{ $theme_sidebar_bg }};">
                            <div class="space-y-1.5 text-[10px]">
                                <div class="px-1.5 py-1 rounded bg-white/10 font-bold text-white">📊 Dashboard</div>
                                <div class="px-1.5 py-1 rounded hover:bg-white/5 font-semibold">📂 Sager</div>
                                <div class="px-1.5 py-1 rounded hover:bg-white/5">🏢 Kreditorer</div>
                            </div>
                            <div class="text-[9px] text-slate-400 truncate">Admin</div>
                        </div>

                        {{-- MINI SAGSBEHANDLER EDITOR --}}
                        <div class="flex-1 p-3 transition-colors duration-200 space-y-2.5 overflow-hidden" style="background-color: {{ $theme_sag_editor_wrapper_bg }};">
                            
                            {{-- MINI SAG HEADER --}}
                            <div class="p-2.5 rounded-xl text-white flex items-center justify-between shadow-xs transition-colors duration-200" style="background-color: {{ $theme_sag_editor_header }};">
                                <div>
                                    <div class="font-bold text-[11px]">Sag: Hansen vs. Inkasso</div>
                                    <div class="text-[9px] opacity-80">Sagsansvarlig: Medarbejder</div>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-white/20">Aktiv</span>
                            </div>

                            {{-- MINI SAG INDHOLDSKORT --}}
                            <div class="p-3 rounded-xl border border-slate-200/80 space-y-2.5 shadow-xs transition-colors duration-200" style="background-color: {{ $theme_sag_editor_bg }};">
                                <div class="text-[11px] font-bold text-slate-900 border-b border-slate-100 pb-1 flex items-center justify-between">
                                    <span>Sagsnotater & Aktivitet</span>
                                    <span class="text-[9px] text-slate-400 font-normal">Idag 12:45</span>
                                </div>

                                {{-- Dummy notat linjer --}}
                                <div class="space-y-1">
                                    <div class="h-2 bg-slate-200 rounded w-full"></div>
                                    <div class="h-2 bg-slate-100 rounded w-3/4"></div>
                                </div>

                                {{-- GEM SAG KNAP EKSEMPEL --}}
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[9px] text-slate-400">Sidst gemt af Admin</span>
                                    <button 
                                        type="button" 
                                        class="px-3 py-1.5 rounded-lg text-white font-bold text-[10px] shadow-xs transition-colors duration-200 cursor-default"
                                        style="background-color: {{ $theme_primary }};"
                                    >
                                        💾 Gem sag
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- 🔐 2-FAKTOR LOGIN INDSTILLINGER --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6">
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
                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border transition cursor-pointer {{ $two_factor_provider === 'totp' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 hover:bg-slate-50' }}">
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

                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border transition cursor-pointer {{ $two_factor_provider === 'twilio' ? 'border-indigo-500 bg-indigo-50/40' : 'border-slate-200 hover:bg-slate-50' }}">
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

            {{-- STYRING AF 2FA KRAV PR. ROLLE --}}
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kræv 2FA for følgende roller
                    </label>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Afkrydsningsfelterne bestemmer, hvilke brugere der tvinges igennem 2FA ved login.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-1">
                    @foreach($allRoles as $role)
                        <label class="flex items-center justify-between p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition cursor-pointer">
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

    {{-- BUND GEM-KNAP --}}
    <div class="flex justify-end pt-2">
        <button 
            type="button" 
            wire:click="save" 
            class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition cursor-pointer flex items-center gap-2"
        >
            <span>💾</span> Gem Indstillinger
        </button>
    </div>

</div>