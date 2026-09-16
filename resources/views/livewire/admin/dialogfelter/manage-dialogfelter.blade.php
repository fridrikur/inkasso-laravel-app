<div class="max-w-7xl mx-auto space-y-6">

    <!-- HEADER & SØGEFELT -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">🌳 Dialogfelter & Sagstræ</h1>
            <p class="text-xs text-slate-500 mt-1">Hierarkisk overblik over sager, klientinformation, historik og bogholderi.</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Søg på sags-ID, debitor eller titel..." 
                class="w-full sm:w-72 rounded-xl border border-slate-200 px-4 py-2.5 text-xs outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer shrink-0">
                    Nulslit
                </button>
            @endif
        </div>
    </div>

    <!-- FANER FOR DIALOGFELTER -->
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200/80 flex items-center gap-2 overflow-x-auto">
        <button 
            wire:click="$set('activeTab', 'sager')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'sager' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>📂</span>
            <span>Sags-oversigt</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'sager' ? 'bg-indigo-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $sagerCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'bogholderi')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'bogholderi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>💰</span>
            <span>Bogholderi & Poster</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'bogholderi' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $bogholderiCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'historik')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'historik' ? 'bg-amber-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>📜</span>
            <span>Historik & Log</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'historik' ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $historikCount }}</span>
        </button>

        <button 
            wire:click="$set('activeTab', 'klienter')"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === 'klienter' ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            <span>👥</span>
            <span>Klientinfo & Parter</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] {{ $activeTab === 'klienter' ? 'bg-purple-700 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $klienterCount }}</span>
        </button>
    </div>

    <!-- TABELLER FOR DE ENKELTE FANER -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">ID / Sag</th>
                        <th class="px-6 py-3.5">Klient / Debitor</th>
                        <th class="px-6 py-3.5">
                            @if($activeTab === 'bogholderi') Beløb / Saldo
                            @elseif($activeTab === 'historik') Seneste Hændelse
                            @elseif($activeTab === 'klienter') Kontaktoplysninger
                            @else Status / Oprettet
                            @endif
                        </th>
                        <th class="px-6 py-3.5 text-right">Træstruktur Handling</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">

                    {{-- TAB 1: SAGS-OVERSIGT --}}
                    @if($activeTab === 'sager')
                        @forelse($paginatedSager as $sag)
                            <tr wire:key="sag-{{ $sag->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">Sag #{{ $sag->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">
                                    {{ $sag->sagerdebitor->navn ?? 'Ingen debitor' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    Oprettet: {{ $sag->created_at?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openTreeModal({{ $sag->id }})" class="px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition cursor-pointer">
                                            🌳 Se Træstruktur
                                        </button>
                                        <a href="{{ route('sager.edit', $sag) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-bold transition">
                                            Åbn sag ↗
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen sager fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 2: BOGHOLDERI --}}
                    @if($activeTab === 'bogholderi')
                        @forelse($paginatedBogholderi as $sag)
                            <tr wire:key="bog-{{ $sag->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-600">Sag #{{ $sag->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->navn ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800">
                                    {{-- Justér feltnavn efter jeres bogholderimodel (f.eks. hovedstol / saldo) --}}
                                    DKK {{ number_format($sag->hovedstol ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button wire:click="openTreeModal({{ $sag->id }})" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-xs font-bold transition cursor-pointer">
                                        💰 Se Bogholderi-træ
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen bogholderiposter fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 3: HISTORIK --}}
                    @if($activeTab === 'historik')
                        @forelse($paginatedHistorik as $sag)
                            <tr wire:key="hist-{{ $sag->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-amber-600">Sag #{{ $sag->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->navn ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600 italic">
                                    Sidst opdateret: {{ $sag->updated_at?->diffForHumans() ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button wire:click="openTreeModal({{ $sag->id }})" class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 text-xs font-bold transition cursor-pointer">
                                        📜 Se Historik-træ
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen historik fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 4: KLIENTER --}}
                    @if($activeTab === 'klienter')
                        @forelse($paginatedKlienter as $sag)
                            <tr wire:key="klient-{{ $sag->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-purple-600">Sag #{{ $sag->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->navn ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    {{ $sag->debitor->email ?? 'Ingen e-mail' }} | {{ $sag->debitor->tlf ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <button wire:click="openTreeModal({{ $sag->id }})" class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-600 hover:bg-purple-100 text-xs font-bold transition cursor-pointer">
                                        👥 Se Klient-træ
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen klienter fundet.</td></tr>
                        @endforelse
                    @endif

                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/30">
            @if($activeTab === 'sager') {{ $paginatedSager->links() }}
            @elseif($activeTab === 'bogholderi') {{ $paginatedBogholderi->links() }}
            @elseif($activeTab === 'historik') {{ $paginatedHistorik->links() }}
            @elseif($activeTab === 'klienter') {{ $paginatedKlienter->links() }}
            @endif
        </div>
    </div>

    <!-- 🌳 TRÆSTRUKTUR MODAL (DETALJERET OVERBLIK) -->
    @if($selectedSag->sagerdebitor)
    <div class="grid grid-cols-2 gap-2 text-slate-700 font-sans text-xs">
        <div>Navn: <span class="font-bold text-slate-900">{{ $selectedSag->sagerdebitor->navn }}</span></div>
        <div>E-mail: <span class="font-semibold">{{ $selectedSag->sagerdebitor->email ?? '-' }}</span></div>
        <div>Tlf: <span class="font-semibold">{{ $selectedSag->sagerdebitor->tlf ?? '-' }}</span></div>
        <div>pnr/PNR: <span class="font-mono">{{ $selectedSag->sagerdebitor->pnr ?? '-' }}</span></div>
    </div>
@else
    <p class="text-slate-400 italic font-sans">Ingen debitor tilknyttet denne sag.</p>
@endif

</div>