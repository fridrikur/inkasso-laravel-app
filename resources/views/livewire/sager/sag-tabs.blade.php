<div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-sm flex flex-wrap justify-between items-center gap-4">
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

        {{-- 6. DOKUMENTER (Henter direkte via $sag->dokumenter()->count()) --}}
        @php
            $docCount = $sag ? $sag->dokumenter()->count() : 0;
        @endphp
        <button
            type="button"
            wire:click="selectTab('dokumenter')"
            class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 cursor-pointer select-none
                {{ $activeTab === 'dokumenter' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
        >
            <span>📁 Dokumenter</span>
            @if($docCount > 0)
                <span class="inline-flex items-center gap-1 {{ $activeTab === 'dokumenter' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-800' }} text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                    <span>📄</span>
                    <span>{{ $docCount }}</span>
                </span>
            @endif
        </button>

    </nav>

    {{-- 🟢 Download alt-knap (vises når man er på dokument-fanen og der er filer) --}}
    @if($activeTab === 'dokumenter' && $docCount > 0)
        <a href="{{ route('sager.dokumenter.downloadAll', $sag) }}"
           class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-semibold hover:bg-slate-700 transition flex items-center gap-2 shadow-sm mr-2">
            <span>⬇ Download alt (.zip)</span>
        </a>
    @endif
</div>