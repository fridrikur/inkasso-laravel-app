<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Importlog</h1>
            <p class="text-xs text-slate-500 mt-1">
                Oversigt over alle importerede filer, sagsantal og historiske gennemførsler.
            </p>
        </div>
    </div>

    {{-- FILTRERING, SØGNING OG STATUS-FANER --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm space-y-5">
        
        {{-- STATUS FANER (TABS) --}}
        <div class="border-b border-slate-100 pb-4">
            <nav class="flex items-center gap-2 overflow-x-auto text-xs font-semibold">
                {{-- ALLE --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', '')"
                    class="px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap {{ ($statusFilter ?? '') === '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}"
                >
                    📁 Alle importer
                </button>

                {{-- SUCCES --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'completed')"
                    class="px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ ($statusFilter ?? '') === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100/80' }}"
                >
                    <span>✓ Gennemførte</span>
                </button>

                {{-- MED FEJL --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'failed')"
                    class="px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ ($statusFilter ?? '') === 'failed' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100/80' }}"
                >
                    <span>⚠️ Med fejl</span>
                </button>

                {{-- RULLET TILBAGE --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'rolled_back')"
                    class="px-4 py-2 rounded-xl transition cursor-pointer whitespace-nowrap flex items-center gap-1.5 {{ ($statusFilter ?? '') === 'rolled_back' ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200/70' }}"
                >
                    <span>↺ Rullet tilbage</span>
                </button>
            </nav>
        </div>

        {{-- SØGNING OG KREDITOR FILTER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            
            {{-- SØGNING --}}
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    🔍
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Søg på filnavn eller kreditor..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                >
            </div>

            {{-- KREDITOR OG PER PAGE FILTER --}}
            <div class="flex items-center gap-3">
                <select
                    wire:model.live="kreditorFilter"
                    class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 outline-none cursor-pointer"
                >
                    <option value="">Alle kreditorer</option>
                    @foreach($kreditorer as $kreditor)
                        <option value="{{ $kreditor->id }}">{{ $kreditor->navn }}</option>
                    @endforeach
                </select>

                <select
                    wire:model.live="perPage"
                    class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-700 outline-none cursor-pointer"
                >
                    <option value="15">15 / side</option>
                    <option value="30">30 / side</option>
                    <option value="50">50 / side</option>
                </select>
            </div>

        </div>
    </div>

    {{-- TABEL OVER IMPORT-SESSIONER --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px] bg-slate-50/50">
                        <th class="py-3.5 px-6">Dato & Tid</th>
                        <th class="py-3.5 px-6">Filnavn</th>
                        <th class="py-3.5 px-6">Kreditor</th>
                        <th class="py-3.5 px-6 text-center">Status & Resultat</th>
                        <th class="py-3.5 px-6 text-right">Handling</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50/60 transition">
                            {{-- DATO & TID --}}
                            <td class="py-3.5 px-6 font-mono text-slate-500 whitespace-nowrap">
                                {{ $session->created_at?->format('d/m-Y H:i') ?? '-' }}
                            </td>

                            {{-- FILNAVN --}}
                            <td class="py-3.5 px-6 font-bold text-slate-900">
                                <span class="inline-flex items-center gap-1.5">
                                    <span>📄</span>
                                    <span>{{ basename($session->file_path) }}</span>
                                </span>
                            </td>

                            {{-- KREDITOR --}}
                            <td class="py-3.5 px-6 font-semibold text-slate-800">
                                {{ $session->kreditor?->navn ?? 'Ukendt kreditor' }}
                            </td>

                            {{-- STATUS & TAL --}}
                            <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($session->status === 'rolled_back')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200/60 font-semibold text-[10px]">
                                            ↺ Rullet tilbage
                                        </span>
                                    @else
                                        {{-- INDSATTE --}}
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold text-[10px]">
                                            ✓ {{ $session->inserted ?? 0 }} indsat
                                        </span>

                                        {{-- FEJLEDE --}}
                                        @if(($session->failed ?? 0) > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 font-semibold text-[10px]">
                                                ⚠️ {{ $session->failed }} fejlede
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- HANDLING --}}
                            <td class="py-3.5 px-6 text-right whitespace-nowrap space-x-2">
                                {{-- SE DETALJER --}}
                                <a
                                    href="{{ route('sager.import.session', $session->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 text-indigo-600 font-bold text-[11px] transition shadow-sm"
                                >
                                    <span>Se detaljer &rarr;</span>
                                </a>

                                {{-- FORSØG IGEN (Genbruger den oprindelige fil) --}}
                                @if($session->status === 'rolled_back' || ($session->failed ?? 0) > 0)
                                    <a
                                        href="{{ route('sager.import.session.retry', $session) }}"
                                        title="Genstart import med den oprindelige fil"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 font-bold text-[11px] transition shadow-sm"
                                    >
                                        <span>🔄 Forsøg igen</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 italic">
                                Ingen import-sessioner fundet i loggen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/30">
            {{ $sessions->links() }}
        </div>
    </div>
</div>