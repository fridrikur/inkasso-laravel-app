<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER & SØGEFELT -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">🏢 Administrer Debitorer</h1>
            <p class="text-xs text-slate-500 mt-1">Overblik over aktive debitorer, forældreløse poster og systemdubletter.</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Søg på navn, e-mail eller pnr/PNR..." 
                class="w-full sm:w-72 rounded-xl border border-slate-200 px-4 py-2.5 text-xs outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer shrink-0">
                    Nulstil
                </button>
            @endif
        </div>
    </div>

    <!-- FANER FOR DEBITORER -->
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200/80 flex items-center gap-2 overflow-x-auto">
        <button 
            wire:click="$set('activeTab', 'active')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>📁</span>
            <span>Med sager</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'active' ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $activeCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'orphans')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'orphans' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>⚠️</span>
            <span>Uden sager (Forældreløse)</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'orphans' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $orphansCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'same_name')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'same_name' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>👥</span>
            <span>Dubletter: Samme navn</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'same_name' ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $sameNameCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'same_pnr')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'same_pnr' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>🔍</span>
            <span>Dubletter: Samme pnr / PNR</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'same_pnr' ? 'bg-rose-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $samepnrCount }}</span>
        </button>
    </div>

    <!-- TABELLER -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">ID</th>
                        <th class="px-6 py-3.5">Debitor / Navn</th>
                        @if($activeTab === 'active')
                            <th class="px-6 py-3.5">Tilknyttede sager</th>
                        @elseif($activeTab === 'same_name')
                            <th class="px-6 py-3.5">pnr / PNR</th>
                        @elseif($activeTab === 'same_pnr')
                            <th class="px-6 py-3.5">pnr / PNR (Dublet)</th>
                        @else
                            <th class="px-6 py-3.5">Status</th>
                        @endif
                        <th class="px-6 py-3.5 text-right">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">

                    {{-- TAB 1: MED SAGER --}}
                    @if($activeTab === 'active')
                        @forelse($activeDebitorer as $debitor)
                            <tr wire:key="deb-{{ $debitor->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ $debitor->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <button wire:click="openDebitorModal({{ $debitor->id }})" class="hover:text-indigo-600 transition text-left cursor-pointer">
                                        {{ $debitor->navn }}
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($debitor->sager as $sag)
                                            <a href="{{ route('sager.edit', $sag) }}" class="px-2 py-0.5 rounded-lg bg-slate-100 text-xs font-mono font-bold text-slate-700 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition">Sag #{{ $sag->id }}</a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-table-actions :id="$debitor->id" editAction="openDebitorModal" deleteAction="deleteDebitor" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen aktive debitorer fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 2: ORPHANS --}}
                    @if($activeTab === 'orphans')
                        @forelse($orphans as $debitor)
                            <tr wire:key="deb-{{ $debitor->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ $debitor->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <button wire:click="openDebitorModal({{ $debitor->id }})" class="hover:text-indigo-600 transition text-left cursor-pointer">
                                        {{ $debitor->navn }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 italic">Ingen sager tilknyttet</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-table-actions :id="$debitor->id" editAction="openDebitorModal" deleteAction="deleteDebitor" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen forældreløse debitorer fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 3: SAMME NAVN --}}
                    @if($activeTab === 'same_name')
                        @forelse($sameNameDebitorer as $debitor)
                            <tr wire:key="deb-{{ $debitor->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ $debitor->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <button wire:click="openDebitorModal({{ $debitor->id }})" class="hover:text-indigo-600 transition text-left cursor-pointer">
                                        {{ $debitor->navn }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $debitor->pnr ?? $debitor->pnr ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-table-actions :id="$debitor->id" editAction="openDebitorModal" deleteAction="deleteDebitor" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen navne-dubletter fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 4: SAMME pnr --}}
                    @if($activeTab === 'same_pnr')
                        @forelse($samepnrDebitorer as $debitor)
                            <tr wire:key="deb-{{ $debitor->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">#{{ $debitor->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    <button wire:click="openDebitorModal({{ $debitor->id }})" class="hover:text-indigo-600 transition text-left cursor-pointer">
                                        {{ $debitor->navn }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-rose-600">{{ $debitor->pnr ?? $debitor->pnr ?? '-' }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <x-table-actions :id="$debitor->id" editAction="openDebitorModal" deleteAction="deleteDebitor" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen pnr/PNR-dubletter fundet.</td></tr>
                        @endforelse
                    @endif

                </tbody>
            </table>
        </div>

        {{-- PAGINATION LINKS --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/30">
            @if($activeTab === 'active')
                {{ $activeDebitorer->links() }}
            @elseif($activeTab === 'orphans')
                {{ $orphans->links() }}
            @elseif($activeTab === 'same_name')
                {{ $sameNameDebitorer->links() }}
            @elseif($activeTab === 'same_pnr')
                {{ $samepnrDebitorer->links() }}
            @endif
        </div>
    </div>

    <!-- MODAL: DETALJER -->
    @if($showModal && $selectedDebitor)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div wire:click="closeModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-100">
                    
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">🏢</span>
                            <h3 class="text-base font-bold text-slate-900">Debitor Detaljer: {{ $selectedDebitor->navn }}</h3>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">ID</span>
                                <span class="font-mono font-semibold text-slate-800">#{{ $selectedDebitor->id }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Navn</span>
                                <span class="font-semibold text-slate-800">{{ $selectedDebitor->navn }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">pnr / PNR</span>
                                <span class="font-mono font-semibold text-slate-800">{{ $selectedDebitor->pnr ?? $selectedDebitor->pnr ?? 'Ikke angivet' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">E-mail</span>
                                <span class="font-semibold text-slate-800">{{ $selectedDebitor->email ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Adresse</span>
                                <span class="font-semibold text-slate-800">{{ $selectedDebitor->adresse ?? '-' }}</span>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <span class="block text-slate-400 font-bold uppercase tracking-wider mb-1">Telefon / Mobil</span>
                                <span class="font-semibold text-slate-800">{{ $selectedDebitor->tlf ?? $selectedDebitor->mobil ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tilknyttede Sager ({{ $selectedDebitor->sager->count() }})</h4>
                            @if($selectedDebitor->sager->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($selectedDebitor->sager as $sag)
                                        <a href="{{ route('sager.edit', $sag) }}" target="_blank" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition">
                                            Sag #{{ $sag->id }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-400 italic">Ingen sager tilknyttet denne debitor.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs cursor-pointer">Luk</button>
                        <a href="{{ route('debitorer.edit', $selectedDebitor) }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-xs cursor-pointer shadow-sm">Gå til redigering</a>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>