<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
    {{-- 1. Modtaget & 2. Status (Side om side) --}}
    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modtaget</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none font-mono">
            {{ optional($currentSag->modtaget)->format('d-m-Y H:i') ?? '-' }}
        </div>
    </div>

    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->status?->tekst ?? 'Ikke angivet' }}
        </div>
    </div>

    {{-- 3. Debitor navn (Fuld bredde) --}}
    <div class="space-y-1 md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Debitor</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->debitor->first()?->navn ?? '-' }}
        </div>
    </div>

    {{-- 4. C/O (Fuld bredde) --}}
    <div class="space-y-1 md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">C/O</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->debitor->first()?->co ?? '-' }}
        </div>
    </div>

    {{-- 5. Adresse (Fuld bredde) --}}
    <div class="space-y-1 md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Adresse</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->debitor->first()?->adresse ?? '-' }}
        </div>
    </div>

    {{-- 6. Post nr. & 7. By (Side om side) --}}
    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Post nr.</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->debitor->first()?->postnr ?? '-' }}
        </div>
    </div>

    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">By</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ optional($currentSag->debitor->first()?->postnummer)->by ?? '-' }}
        </div>
    </div>

    {{-- 8. Aktiv (Fuld bredde som aftalt) --}}
    <div class="space-y-1 md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aktiv</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->aktiv ?? '-' }}
        </div>
    </div>

    {{-- 9. Stel nr. & 10. Kontrakt/Gæld (Side om side) --}}
    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stel nr.</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none font-mono">
            {{ $currentSag->stelnr ?? '-' }}
        </div>
    </div>

    <div class="space-y-1">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kontrakt/Gæld</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
            {{ $currentSag->ktr->first()?->tekst ?? '-' }}
        </div>
    </div>

    {{-- 11. DKG Sagsbehandler (Fuld bredde pga. kontaktinfo grid) --}}
    <div class="space-y-1 md:col-span-2">
        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">DKG Sagsbehandler</label>
        <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-slate-800 select-none space-y-1">
            @if($currentSag->first_konsulent)
                <div class="font-bold text-slate-900 flex items-center gap-1.5 text-sm">
                    <span>👤</span>
                    <span>{{ $currentSag->first_konsulent->navn }}</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] text-slate-600 border-t border-slate-200/60 pt-1.5">
                    @if($currentSag->first_konsulent->email)
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400">✉️</span>
                            <a href="mailto:{{ $currentSag->first_konsulent->email }}" class="text-indigo-600 hover:underline truncate">
                                {{ $currentSag->first_konsulent->email }}
                            </a>
                        </div>
                    @endif
                    @if($currentSag->first_konsulent->tlf)
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400">📞</span>
                            <a href="tel:{{ $currentSag->first_konsulent->tlf }}" class="hover:underline">
                                {{ $currentSag->first_konsulent->tlf }}
                            </a>
                        </div>
                    @endif
                    @if($currentSag->first_konsulent->mobil)
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400">📱</span>
                            <a href="tel:{{ $currentSag->first_konsulent->mobil }}" class="hover:underline">
                                {{ $currentSag->first_konsulent->mobil }}
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <span class="text-slate-400">-</span>
            @endif
        </div>
    </div>
</div>