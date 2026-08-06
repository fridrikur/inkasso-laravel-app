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

        {{-- 3. DIALOG & NOTER (SAMLET) --}}
        <button
            type="button"
            wire:click="selectTab('dialog')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'dialog' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>💬 Dialog & Noter</span>
            @if(($unreadKlientinfo + $unreadHistorik + $unreadBogholderi) > 0)
                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-1.5 py-0.5 rounded-full animate-bounce">
                    +{{ $unreadKlientinfo + $unreadHistorik + $unreadBogholderi }}
                </span>
            @endif
        </button>

        {{-- 4. BOGHOLDERI (POSTERINGER) --}}
        <button
            type="button"
            wire:click="selectTab('bogholderi')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'bogholderi' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>💳 Bogholderi</span>
        </button>

    </nav>
</div>