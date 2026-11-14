<div class="max-w-5xl mx-auto space-y-6 p-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Brev-felter & Filter</h1>
            <p class="text-xs text-slate-500 mt-0.5">Styr hvilke felter der skal være tilgængelige som flettefelter i brev-editoren.</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($felter as $kategori => $gruppeFelter)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
                <h2 class="text-sm font-bold text-indigo-600 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Kategori: {{ $kategori }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($gruppeFelter as $felt)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">{{ $felt->visnings_navn }}</span>
                                <code class="text-[11px] text-indigo-600 font-mono">{{ '{' . $felt->felt_navn . '}' }}</code>
                            </div>

                            {{-- Alpine / Livewire Toggle Switch --}}
                            <button 
                                type="button"
                                wire:click="toggleFelt({{ $felt->id }})"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $felt->aktiv ? 'bg-indigo-600' : 'bg-slate-300' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out {{ $felt->aktiv ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>