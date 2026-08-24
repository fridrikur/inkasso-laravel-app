<div class="space-y-6">

    {{-- HEADER & OPRET KNAP --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                <span>🏢</span> Administration af Kreditorer
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Styr kreditorers stamdata, portalbrugere og tilknyttede sagsbehandlere.
            </p>
        </div>

        <button 
            type="button" 
            wire:click="opretnykreditor" 
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer shrink-0"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Ny kreditor</span>
        </button>
    </div>

    {{-- DYNAMISKE STATUS-FANER / QUICK-FILTERS --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50/80 p-2 rounded-2xl border border-slate-200/80">
        <div class="flex flex-wrap items-center gap-1.5">
            <button
                type="button"
                wire:click="setFilter('all')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 select-none cursor-pointer
                    {{ $filter === 'all' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/80' : 'text-slate-600 hover:bg-white/60' }}"
            >
                <span>Alle kreditorer</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono {{ $filter === 'all' ? 'bg-slate-100 text-slate-700' : 'bg-slate-200/60 text-slate-600' }}">
                    {{ $totalKreditorer }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setFilter('active_cases')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 select-none cursor-pointer
                    {{ $filter === 'active_cases' ? 'bg-white text-amber-900 shadow-sm border border-amber-200/80' : 'text-slate-600 hover:bg-white/60' }}"
            >
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span>Med sager</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono bg-amber-50 text-amber-700">
                    {{ $medSagerCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setFilter('no_cases')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 select-none cursor-pointer
                    {{ $filter === 'no_cases' ? 'bg-white text-emerald-900 shadow-sm border border-emerald-200/80' : 'text-slate-600 hover:bg-white/60' }}"
            >
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Uden sager</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono bg-emerald-50 text-emerald-700">
                    {{ $udenSagerCount }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setFilter('with_users')"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 select-none cursor-pointer
                    {{ $filter === 'with_users' ? 'bg-white text-indigo-900 shadow-sm border border-indigo-200/80' : 'text-slate-600 hover:bg-white/60' }}"
            >
                <span>👤 Med portalbrugere</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-mono bg-indigo-50 text-indigo-700">
                    {{ $medBrugereCount }}
                </span>
            </button>
        </div>

        {{-- SØGEFELT --}}
        <div class="relative min-w-[240px] shrink-0">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Søg kreditor eller Lotus ID..." 
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 focus:outline-hidden transition"
            >
            @if($search)
                <button 
                    type="button" 
                    wire:click="$set('search', '')" 
                    class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs font-bold"
                >
                    ✕
                </button>
            @endif
        </div>
    </div>

    {{-- TABELOVERSIGT over KREDITORER --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 border-collapse">
                <thead class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th scope="col" class="px-6 py-4">Kreditor Navn / Identifikation</th>
                        <th scope="col" class="px-6 py-4">Tilknytninger</th>
                        <th scope="col" class="px-6 py-4">Oprettet</th>
                        <th scope="col" class="px-6 py-4 text-right">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($kreditorer as $kreditor)
                        <tr wire:key="kreditor-{{ $kreditor->id }}" class="hover:bg-slate-50/60 transition duration-150">
                            
                            {{-- NAVN OG ID --}}
                            <td class="px-6 py-4">
                                <a 
                                    href="{{ route('kreditor.manage', $kreditor) }}"
                                    class="font-bold text-slate-900 text-sm hover:text-indigo-600 transition flex items-center gap-2 group"
                                >
                                    <span>{{ $kreditor->navn }}</span>
                                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 text-indigo-500 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>

                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-[11px] font-mono text-slate-400">#{{ $kreditor->id }}</span>
                                    @if($kreditor->lotusID)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200/60 text-[10px] font-mono">
                                            Lotus: {{ $kreditor->lotusID }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- TILKNYTNINGER --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a 
                                        href="{{ route('admin.sager.status.show', ['status' => 1, 'kreditor_id' => $kreditor->id]) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold transition
                                            {{ $kreditor->sager_count > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200/80 hover:bg-amber-100' : 'bg-slate-50 text-slate-400 border border-slate-200/50' }}"
                                        title="Se sager for denne kreditor opdelt efter status"
                                    >
                                        <span>📂 {{ $kreditor->sager_count }} sager</span>
                                    </a>

                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-semibold border border-slate-200/60" title="Tilknyttede portalbrugere">
                                        👤 {{ $kreditor->users_count }} brugere
                                    </span>

                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-semibold border border-slate-200/60" title="Tilknyttede sagsbehandlere">
                                        📞 {{ $kreditor->sagsbehandlere_count }} sagsbehandlere
                                    </span>
                                </div>
                            </td>

                            {{-- OPRETTET DATO --}}
                            <td class="px-6 py-4 font-mono text-slate-500 text-xs whitespace-nowrap">
                                {{ $kreditor->created_at?->format('d-m-Y') ?? '-' }}
                            </td>

                            {{-- 🟢 REN OG PÆN HANDLINGER KOLONNE MED X-TABLE-ACTIONS --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <x-table-actions 
                                    :id="$kreditor->id" 
                                    :viewUrl="route('kreditor.manage', $kreditor)"
                                    editAction="editKreditor"
                                    deleteAction="requestDelete"
                                />
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                Ingen kreditorer fundet med det valgte filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $kreditorer->links() }}
        </div>
    </div>

    {{-- KREDITOR OPRET/REDIGER FORM MODAL (Sub-komponent) --}}
    @livewire('kreditor.kreditor-form-modal')

    {{-- SLET KREDITOR MODAL --}}
    @if($showDeleteModal && $kreditorToDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button type="button" wire:click="closeModals" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">&times;</button>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Slet kreditor</h3>
                        <p class="text-xs font-semibold text-slate-500 font-mono">{{ $kreditorToDelete->navn }}</p>
                    </div>
                </div>

                @if($sagerCount > 0)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-3">
                        <p class="text-xs text-amber-800 font-medium">Kreditoren har {{ $sagerCount }} aktive sager. Vælg modtager og indtast sikkerhedskode:</p>
                        
                        {{-- Vælg modtager --}}
                        <div class="space-y-1">
                            <select wire:model="transferToKreditorId" class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 focus:outline-hidden">
                                <option value="">-- Vælg modtager-kreditor --</option>
                                @foreach($transferTargets as $target)
                                    <option value="{{ $target->id }}">{{ $target->navn }}</option>
                                @endforeach
                            </select>
                            @error('transferToKreditorId') <span class="text-[10px] text-rose-600 font-bold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Sikkerhedskode felt --}}
                        <div class="space-y-1">
                            <input 
                                type="password" 
                                wire:model="securityCode" 
                                placeholder="Indtast global sikkerhedskode" 
                                class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 focus:outline-hidden"
                            >
                            @error('securityCode') <span class="text-[10px] text-rose-600 font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-600">Er du sikker på, at du vil slette denne kreditor? Handlingen kan ikke fortrydes.</p>
                @endif

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeModals" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">Annuller</button>
                    
                    <button 
                        type="button" 
                        wire:click="confirmDelete" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer disabled:opacity-50"
                    >
                        <svg wire:loading wire:target="confirmDelete" class="h-3.5 w-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="confirmDelete">Slet kreditor</span>
                        <span wire:loading wire:target="confirmDelete">Arbejder...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>