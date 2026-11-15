<div class="max-w-5xl mx-auto space-y-6 p-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Brev-felter & Filter</h1>
            <p class="text-xs text-slate-500 mt-0.5">Styr hvilke felter der skal være tilgængelige som flettefelter i brev-editoren.</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($felter as $kategori => $gruppeFelter)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4" wire:key="kategori-{{ $kategori }}">
                
                {{-- Kategori Header med Vælg alle / Fravælg alle knapper --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h2 class="text-sm font-bold text-indigo-600 uppercase tracking-wider">
                        Kategori: {{ $kategori }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="setKategoriStatus('{{ $kategori }}', true)"
                            class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition cursor-pointer"
                        >
                            Vælg alle
                        </button>
                        <button 
                            type="button" 
                            wire:click="setKategoriStatus('{{ $kategori }}', false)"
                            class="text-[11px] font-bold text-slate-500 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg transition cursor-pointer"
                        >
                            Fravælg alle
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($gruppeFelter as $felt)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100" wire:key="felt-item-{{ $felt->id }}">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">{{ $felt->visnings_navn }}</span>
                                <code class="text-[11px] text-indigo-600 font-mono">{{ '{' . $felt->felt_navn . '}' }}</code>
                            </div>

                            {{-- Den robuste toggle switch --}}
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:key="felt-input-{{ $felt->id }}"
                                    wire:click="toggleFelt({{ $felt->id }})" 
                                    @checked($felt->aktiv) 
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>