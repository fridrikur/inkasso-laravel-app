<div class="space-y-6">

    {{-- HEADER MED STATUSTITEL OG EKSPORT --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                {{ $status->tekst ?? $status->navn ?? 'Sagsstatus' }}
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Viser sager i status <strong class="text-slate-800">{{ $status->tekst ?? $status->navn }}</strong>
                @if($selectedKreditor)
                    for kreditor <strong class="text-indigo-600">{{ $selectedKreditor->navn }}</strong>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Totalt: {{ $totalCount }} sager
            </span>

            <button
                type="button"
                wire:click="$set('showExportModal', true)"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Eksporter CSV</span>
            </button>
        </div>
    </div>

    {{-- 🏷️ STATUS FANER --}}
    <div class="space-y-2">
        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
            Vælg Status-tilstand
        </label>
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-3">
            @foreach($allStatuses as $st)
                <button
                    type="button"
                    wire:click="setStatus({{ $st->id }})"
                    class="px-3.5 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                        {{ $status->id === $st->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    <span>{{ $st->tekst ?? $st->navn }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- 🏢 KREDITOR FANER --}}
    <div class="space-y-2 pt-1">
        <div class="flex items-center justify-between">
            <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">
                Filtrer på Kreditor
            </label>
            @if($kreditor_id)
                <button 
                    type="button" 
                    wire:click="setKreditor(null)"
                    class="text-xs text-indigo-600 font-semibold hover:underline cursor-pointer"
                >
                    Vis alle kreditorer
                </button>
            @endif
        </div>

        <div class="flex flex-wrap gap-1.5 p-2 bg-slate-50 rounded-2xl border border-slate-200/80">
            <button
                type="button"
                wire:click="setKreditor(null)"
                class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer
                    {{ $kreditor_id === null ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}"
            >
                <span>Samtlige Kreditorer</span>
            </button>

            @foreach($allKreditors as $kred)
                @php $isActive = $kreditor_id === $kred->id; @endphp
                <button
                    type="button"
                    wire:click="setKreditor({{ $kred->id }})"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer
                        {{ $isActive ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}"
                >
                    <span>{{ $kred->navn }}</span>
                    <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-mono font-bold
                        {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $kred->sager_count }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- 🟢 SAGER DATA TABLE MED MIGRERET STATUS OG KREDITOR FILTRERING --}}
    <div wire:key="status-table-container-{{ $status->id }}-{{ $kreditor_id ?? 'all' }}">
        @livewire('sager.sager-data-table', [
            'mode' => 'status',
            'statusId' => $status->id,
            'selectedKreditor' => $selectedKreditor?->navn,
            'uiMode' => 'table',
        ], key('sager-table-' . $status->id . '-' . ($kreditor_id ?? 'all')))
    </div>

    {{-- MODAL FOR KOLONNEVALG TIL CSV EKSPORT --}}
    @if($showExportModal)
        <div class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 max-w-lg w-full space-y-5">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl font-bold">📊</span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Vælg felt-kolonner til CSV</h3>
                            <p class="text-xs text-slate-500">
                                Status <strong class="text-indigo-600">{{ $status->tekst ?? $status->navn }}</strong> inkluderes i filen.
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        wire:click="$set('showExportModal', false)"
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($availableColumns as $key => $label)
                        @if($kreditor_id && $key === 'kreditor')
                            @continue
                        @endif

                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer select-none transition">
                            <input 
                                type="checkbox" 
                                value="{{ $key }}" 
                                wire:model="selectedColumns"
                                class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/20 h-4 w-4"
                            >
                            <span class="text-xs font-semibold text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="$set('showExportModal', false)"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button
                        type="button"
                        wire:click="exportCsv"
                        wire:click.then="$set('showExportModal', false)"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer"
                    >
                        <span>Hent CSV-fil</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>