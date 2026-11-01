<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-xs font-semibold mb-2">
                    📂 Sagsoversigt
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Alle sager
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Fuld oversigt over dine sager, status og udvikling hos DKG.
                </p>
            </div>

            <div>
                <a
                    href="{{ route('kreditor.sag.create') }}"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Opret ny sag</span>
                </a>
            </div>
        </div>

        {{-- SØGEBAR --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <div wire:loading wire:target="search" class="text-xs font-semibold text-indigo-600 flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Søger i sager...
            </div>

            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    🔍
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Søg efter sagsnummer, debitor navn, adresse eller by..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition"
                >
            </div>

            @if($suggestion)
                <div class="text-xs text-slate-600 pt-1">
                    Mente du:
                    <button
                        wire:click="$set('search', '{{ $suggestion }}')"
                        class="text-indigo-600 font-bold underline hover:text-indigo-800 transition"
                    >
                        {{ $suggestion }}
                    </button>
                    ?
                </div>
            @endif
        </div>

        {{-- FUNNEL / STATUS FANER MED TOGGLE TIL AFSLUTNINGER OG BEMÆRKNINGER --}}
        <div x-data="{ showAfslutninger: {{ (str_starts_with($statusFilter ?? '', 'afslutning_') || str_starts_with($statusFilter ?? '', 'bemaerkning_')) ? 'true' : 'false' }} }" class="space-y-3">
            
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                
                {{-- Alle sager --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'all'); showAfslutninger = false;"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    <span>📋 Alle sager</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($statusFilter ?? 'all') === 'all' ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $countAll ?? 0 }}
                    </span>
                </button>

                {{-- Aktive sager --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'active'); showAfslutninger = false;"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    <span>🟢 Aktive sager</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($statusFilter ?? 'all') === 'active' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-100 text-slate-600' }}">
                        {{ $countActive ?? 0 }}
                    </span>
                </button>

                {{-- Afventer DKG --}}
                <button
                    type="button"
                    wire:click="$set('statusFilter', 'pending'); showAfslutninger = false;"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    <span>⏳ Afventer DKG</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ ($statusFilter ?? 'all') === 'pending' ? 'bg-amber-700 text-amber-100' : 'bg-slate-100 text-slate-600' }}">
                        {{ $countPending ?? 0 }}
                    </span>
                </button>

                {{-- AFSLUTTEDE SAGER (TOGGLE) --}}
                @php
                    $isClosedActive = ($statusFilter ?? '') === 'closed' || str_starts_with($statusFilter ?? '', 'afslutning_') || str_starts_with($statusFilter ?? '', 'bemaerkning_');
                @endphp
                <button
                    type="button"
                    @click="showAfslutninger = !showAfslutninger"
                    wire:click="$set('statusFilter', 'closed')"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ $isClosedActive ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
                >
                    <span>🏁 Afsluttede sager</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isClosedActive ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-600' }}">
                        {{ $countClosed ?? 0 }}
                    </span>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': showAfslutninger }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

            </div>

            {{-- UNDER-PANEL: UDFOLDELIGE KNAPPER NÅR AFSLUTTEDE SAGER ER AKTIVERET --}}
            <div 
                x-show="showAfslutninger" 
                x-cloak 
                class="space-y-3 pt-2 pb-3 px-4 bg-slate-50 rounded-2xl border border-slate-200/80 transition-all duration-200"
            >
                {{-- Afslutningsårsager --}}
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Afslutningsårsag:</span>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($afslutningsTyper as $type)
                            @php
                                $filterKey = 'afslutning_' . $type->id;
                                $isTypeActive = ($statusFilter ?? '') === $filterKey;
                            @endphp
                            <button
                                type="button"
                                wire:click="$set('statusFilter', '{{ $filterKey }}')"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-2 {{ $isTypeActive ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200/60' }}"
                            >
                                <span>{{ $type->tekst }}</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isTypeActive ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $countsAfslutning[$type->id] ?? 0 }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Bemærkninger / Status --}}
                @if(isset($bemaerkningTyper) && $bemaerkningTyper->isNotEmpty())
                    <div class="border-t border-slate-200/60 pt-2.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Bemærkning:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($bemaerkningTyper as $bem)
                                @php
                                    $bemFilterKey = 'bemaerkning_' . $bem->id;
                                    $isBemActive = ($statusFilter ?? '') === $bemFilterKey;
                                @endphp
                                <button
                                    type="button"
                                    wire:click="$set('statusFilter', '{{ $bemFilterKey }}')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-2 {{ $isBemActive ? 'bg-slate-900 text-white shadow-sm font-bold' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200/60' }}"
                                >
                                    <span>{{ $bem->tekst }}</span>
                                    @if(isset($countsBemaerkning[$bem->id]))
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isBemActive ? 'bg-slate-800 text-slate-200' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $countsBemaerkning[$bem->id] }}
                                        </span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- SAAS-STYLE DATATABEL --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Sagsliste
                </h2>
                <span class="text-xs text-slate-500 font-medium">
                    Viser filtrerede sager
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px] bg-slate-50/40">
                            <th class="py-3.5 px-6">Sagsnr.</th>
                            <th class="py-3.5 px-6">Debitor</th>
                            <th class="py-3.5 px-6">Kontrakttype</th>
                            <th class="py-3.5 px-6">Modtaget</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Handling</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/80 text-slate-700">
                        @forelse($sager as $sag)
                            <tr
                                id="sag-row-{{ $sag->id }}"
                                class="hover:bg-indigo-50/30 transition-colors duration-150 group"
                            >
                                {{-- SAGSNR --}}
                                <td class="py-4 px-6 font-bold text-slate-900 font-mono tracking-tight">
                                    #{{ $sag->sagsnr }}
                                </td>

                                {{-- DEBITOR --}}
                                <td class="py-4 px-6 font-medium text-slate-900">
                                    {{ $sag->debitor->first()?->navn ?? 'Ingen debitor' }}
                                </td>

                                {{-- KONTRAKTTYPE --}}
                                <td class="py-4 px-6 text-slate-600">
                                    {{ $sag->ktr->first()?->tekst ?? '-' }}
                                </td>

                                {{-- MODTAGET --}}
                                <td class="py-4 px-6 text-slate-500 font-mono text-[11px]">
                                    {{ optional($sag->modtaget ?? $sag->created_at)->format('d/m-Y') }}
                                </td>

                                {{-- STATUS --}}
                                <td class="py-4 px-6">
                                    <div class="space-y-1 text-xs text-slate-700">
                                        @if($sag->afslutning->isNotEmpty())
                                            <div>
                                                <span class="text-slate-400 font-medium">Afslutning:</span> 
                                                <span class="text-slate-900 font-semibold">{{ $sag->afslutning->first()->tekst }}</span>
                                            </div>
                                        @elseif($sag->afsluttet)
                                            <div>
                                                <span class="text-slate-400 font-medium">Status:</span> 
                                                <span class="text-slate-600 font-semibold">Afsluttet</span>
                                            </div>
                                        @else
                                            <div>
                                                @if($sag->konsulent->isEmpty())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200/60 font-semibold text-[10px]">
                                                        Afventer DKG
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold text-[10px]">
                                                        Aktiv
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($sag->afsluttet)
                                            <div class="text-[11px] text-slate-500">
                                                <span class="text-slate-400">Afsluttet:</span> 
                                                <span class="font-mono text-slate-600">{{ \Carbon\Carbon::parse($sag->afsluttet)->format('d/m-Y') }}</span>
                                            </div>
                                        @endif

                                        @if($sag->bemaerkning->isNotEmpty() && $sag->bemaerkning->first()->tekst !== 'Ingen')
                                            <div class="text-[11px] text-slate-500 max-w-xs truncate" title="{{ $sag->bemaerkning->first()->tekst }}">
                                                <span class="text-slate-400">Bemærkning:</span> {{ $sag->bemaerkning->first()->tekst }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                            {{-- HANDLING --}}
                            <td class="py-4 px-6 text-right">
                                <button 
                                    type="button"
                                    wire:click="openSag({{ $sag->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200/80 bg-white hover:border-indigo-500 hover:bg-indigo-50 text-indigo-600 font-bold text-[11px] shadow-2xs transition-all duration-150 cursor-pointer"
                                >
                                    <span>Åbn</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    Ingen sager fundet under dette filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $sager->links() }}
        </div>
    </div>
    {{-- Indsæt slide-over drawer partial --}}
    @include('livewire.sager.partials.drawer')
</div>