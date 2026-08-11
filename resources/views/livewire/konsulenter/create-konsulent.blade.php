<div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Opret Ny Konsulent
            </h1>
            <p class="text-slate-500 mt-0.5 text-xs">
                Opret en ny konsulent og tildel specielle roller
            </p>
        </div>

        <a 
            href="{{ route('konsulenter.index') }}" 
            class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
        >
            ← Tilbage til oversigt
        </a>
    </div>

    {{-- FORMULAR CARD --}}
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 p-6">
        <form wire:submit="save" class="space-y-6">

            {{-- NAVN OG EMAIL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Navn *
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="navn" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none" 
                        placeholder="Indtast fulde navn"
                    >
                    @error('navn') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        E-mail *
                    </label>
                    <input 
                        type="email" 
                        wire:model.blur="email" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none" 
                        placeholder="konsulent@firma.dk"
                    >
                    @error('email') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            {{-- TELEFON OG TITEL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Telefonnummer
                    </label>
                    <input 
                        type="text" 
                        wire:model="tlf" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        placeholder="+45 12 34 56 78"
                    >
                    @error('tlf') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Titel / Stilling
                    </label>
                    <input 
                        type="text" 
                        wire:model="titel" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        placeholder="f.eks. Seniorkonsulent"
                    >
                    @error('titel') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            {{-- 🟢 KONSULENT ROLLER SEKTION --}}
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Særlige Konsulentroller
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    {{-- HOVEDKONSULENT --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model="is_hoved" 
                            class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                        >
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">👑 Hovedkonsulent</span>
                            <span class="text-[10px] text-slate-500 block">Primæransvarlig i systemet</span>
                        </div>
                    </label>

                    {{-- NOTIFIKATIONSKONSULENT --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model="is_notifikation" 
                            class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                        >
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">🔔 Notifikation</span>
                            <span class="text-[10px] text-slate-500 block">Modtager systemadviseringer</span>
                        </div>
                    </label>

                    {{-- SKJULT KONSULENT --}}
                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition cursor-pointer">
                        <input 
                            type="checkbox" 
                            wire:model="is_skjult" 
                            class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                        >
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">👁️ Skjult Konsulent</span>
                            <span class="text-[10px] text-slate-500 block">Skjules i offentlige visninger</span>
                        </div>
                    </label>

                </div>
            </div>

            {{-- AKTIV STATUS TOGGLE --}}
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model="aktiv" 
                        class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                    >
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Aktiv konsulent</span>
                        <span class="text-[11px] text-slate-500">Aktive konsulenter kan vælges på sager og opgaver.</span>
                    </div>
                </label>
            </div>

            {{-- KNAPPER --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a 
                    href="{{ route('konsulenter.index') }}" 
                    class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
                >
                    Annullér
                </a>

                <button 
                    type="submit" 
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition cursor-pointer"
                >
                    Opret konsulent
                </button>
            </div>

        </form>
    </div>

</div>