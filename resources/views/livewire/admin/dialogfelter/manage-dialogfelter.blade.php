<div class="max-w-7xl mx-auto space-y-6 relative">

    <!-- HEADER & SØGEFELT -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">🌳 Dialogfelter & Sagstræ</h1>
            <p class="text-xs text-slate-500 mt-1">Hierarkisk overblik over sager med dialoger, klientinformation og bogholderi.</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Søg på sags-ID, debitor..." 
                class="w-full sm:w-72 rounded-xl border border-slate-200 px-4 py-2.5 text-xs outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition"
            />
            @if($search)
                <button wire:click="$set('search', '')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer shrink-0">
                    Nulstil
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
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->first()?->navn ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">Oprettet: {{ $sag->created_at?->format('d/m/Y') ?? '-' }}</td>
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
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">Ingen sager med dialoger fundet.</td></tr>
                        @endforelse
                    @endif

                    {{-- TAB 2: BOGHOLDERI --}}
                    @if($activeTab === 'bogholderi')
                        @forelse($paginatedBogholderi as $sag)
                            <tr wire:key="bog-{{ $sag->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-emerald-600">Sag #{{ $sag->id }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->first()?->navn ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800">
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
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->first()?->navn ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600 italic">Sidst opdateret: {{ $sag->updated_at?->diffForHumans() ?? '-' }}</td>
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
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $sag->debitor->first()?->navn ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    {{ $sag->debitor->first()?->email ?? 'Ingen e-mail' }} | {{ $sag->debitor->first()?->tlf ?? '-' }}
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


    <!-- 🌳 TRÆSTRUKTUR & DIALOGER MODAL (POP-UP) -->
    @if($showTreeModal && $selectedSag)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-3xl w-full p-6 space-y-6 relative animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span>🌳 Træstruktur for Sag #{{ $selectedSag->id }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Hierarkisk overblik over klientinfo og tilknyttede dialoger.</p>
                </div>
                <button wire:click="closeTreeModal" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs transition cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Content -->
            <div class="space-y-6 max-h-[70vh] overflow-y-auto pr-1">
                
                <!-- Klientinformation -->
                <div>
                    <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Klientinformation</h4>
                    @if($selectedSag->debitor->first())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-slate-700 font-sans text-xs bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>Navn: <br><span class="font-bold text-slate-900">{{ $selectedSag->debitor->first()->navn }}</span></div>
                            <div>E-mail: <br><span class="font-semibold">{{ $selectedSag->debitor->first()->email ?? '-' }}</span></div>
                            <div>Tlf: <br><span class="font-semibold">{{ $selectedSag->debitor->first()->tlf ?? '-' }}</span></div>
                            <div>PNR: <br><span class="font-mono">{{ $selectedSag->debitor->first()->pnr ?? '-' }}</span></div>
                        </div>
                    @else
                        <p class="text-slate-400 italic text-xs font-sans bg-slate-50 p-4 rounded-2xl border border-slate-100">Ingen debitor tilknyttet denne sag.</p>
                    @endif
                </div>

                <!-- Dialoger / Sagsnotater -->
                <div>
                    <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">
                        Dialoger / Sagsnotater ({{ $selectedSag->dialogs->unique('id')->count() }})
                    </h4>
                    
                    @if($selectedSag->dialogs && $selectedSag->dialogs->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($selectedSag->dialogs->unique('id') as $dialog)
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs space-y-2 shadow-xs">
                                    <div class="flex items-center justify-between text-slate-400 text-[10px] border-b border-slate-200/60 pb-1.5">
                                        <span class="font-bold text-slate-700">Dialog-tråd #{{ $dialog->id }}</span>
                                        <span class="font-medium">{{ $dialog->created_at?->format('d/m/Y H:i') ?? '-' }}</span>
                                    </div>

                                    @if($dialog->messages && $dialog->messages->isNotEmpty())
                                        <div class="space-y-2 pt-1">
                                            @foreach($dialog->messages->unique('id') as $msg)
                                                @if(!empty($msg->tekst))
                                                    <div class="bg-white p-3 rounded-xl border border-slate-200/60 space-y-1">
                                                        <div class="text-[10px] text-slate-400 flex justify-between">
                                                            <span>Skrevet: {{ $msg->dato?->format('d/m/Y H:i') ?? $msg->created_at?->format('d/m/Y H:i') }}</span>
                                                        </div>
                                                        <p class="text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $msg->tekst }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-slate-400 italic text-[11px]">Ingen aktive beskedtekster i denne tråd.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-slate-400 italic text-xs font-sans bg-slate-50 p-4 rounded-2xl border border-slate-100">Ingen dialoger fundet på denne sag.</p>
                    @endif
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                <a href="{{ route('sager.edit', $selectedSag) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm">
                    Åbn fuld sag ↗
                </a>
                <button wire:click="closeTreeModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Luk vindue
                </button>
            </div>

        </div>
    </div>
    @endif

</div>