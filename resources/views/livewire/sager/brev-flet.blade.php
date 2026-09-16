<div> {{-- SINGLE ROOT FOR LIVEWIRE --}}

    <div class="space-y-6 relative">

        {{-- TOP BAR: TABS & KNAP TIL SKABELON-ADMINISTRATION --}}
        <div class="border-b border-slate-200 flex flex-wrap gap-2 pb-3 items-center justify-between">

            {{-- Rene tabs til at vælge brev til fletning --}}
            <div class="flex flex-wrap items-center gap-1.5">
                @foreach($breveList as $brev)
                    <button
                        type="button"
                        wire:click="loadBrev({{ $brev['id'] }})"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer
                            {{ $brevId === $brev['id']
                                ? 'bg-indigo-600 text-white shadow-xs'
                                : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100'
                            }}"
                    >
                        {{ $brev['titel'] }}
                    </button>
                @endforeach
            </div>

            {{-- Link-knap over til skabelon-administration (Rediger / Opret skabeloner) --}}
            <div>
                <a
                    href="{{ route('admin.breve.rediger') }}" 
                    class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5"
                >
                    <span>⚙️</span> Rediger / Opret skabeloner
                </a>
            </div>

        </div>

        {{-- Emne (Visning) --}}
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Emne / Overskrift</label>
            <div class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium bg-slate-50 text-slate-800 shadow-2xs">
                {{ $emne ?: 'Ingen emne angivet' }}
            </div>
        </div>

        {{-- Preview Area (Selve det flettede brev som vises direkte til medarbejderen) --}}
        <div class="mt-4">
            <div class="border border-slate-200 p-8 bg-white prose max-w-none min-h-[500px] rounded-2xl shadow-sm">
                {!! $preview !!}
            </div>
        </div>

    </div>
</div>