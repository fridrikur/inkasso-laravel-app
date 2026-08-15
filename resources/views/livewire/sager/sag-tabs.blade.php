<div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-sm">
    <nav class="flex flex-wrap gap-1 items-center">
        
        {{-- 1. SAGSSTAMME --}}
        <button
            type="button"
            wire:click="selectTab('stamdata')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'stamdata' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>📂 Sagsstamme</span>
        </button>

        {{-- 2. BREVE --}}
        <button
            type="button"
            wire:click="selectTab('breve')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'breve' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>📨 Breve</span>
        </button>

        {{-- 3. KLIENTINFORMATION --}}
        <button
            type="button"
            wire:click="selectTab('klientinformation')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'klientinformation' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>👤 Klientinformation</span>
            @if(isset($unreadKlientinfo) && $unreadKlientinfo > 0)
                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full animate-bounce">
                    +{{ $unreadKlientinfo }}
                </span>
            @endif
        </button>

        {{-- 4. HISTORIK --}}
        <button
            type="button"
            wire:click="selectTab('historik')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'historik' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>📜 Historik</span>
            @if(isset($unreadHistorik) && $unreadHistorik > 0)
                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">
                    +{{ $unreadHistorik }}
                </span>
            @endif
        </button>

        {{-- 5. BOGHOLDERI --}}
        <button
            type="button"
            wire:click="selectTab('bogholderi')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'bogholderi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>💳 Bogholderi</span>
            @if(isset($unreadBogholderi) && $unreadBogholderi > 0)
                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full">
                    +{{ $unreadBogholderi }}
                </span>
            @endif
        </button>

    </nav>
</div>