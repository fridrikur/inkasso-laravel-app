<div class="space-y-6">

    {{-- LIVEWIRE INITIAL / GLOBAL LOADER --}}
    <div wire:loading.delay wire:target="search, filterByKreditor, sortBy, gotoPage, nextPage, previousPage, setMode" class="fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 max-w-sm w-full">
            <x-ui-loader type="sager" :count="$modeCount" />
        </div>
    </div>

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                @if($uiMode === 'full' && ($mode === 'full' || $mode === 'all'))
                    Sagsadministration
                @elseif($mode === 'unhandled')
                    Ubehandlede sager
                @elseif($mode === 'incoming')
                    Nyligt indkomne sager
                @elseif($mode === 'active')
                    Aktive sager
                @elseif($mode === 'closed' || $mode === 'afsluttet')
                    Afsluttede sager
                @elseif($mode === 'unread_messages')
                    Sager med ulæste beskeder
                @elseif($mode === 'live_editing')
                    Sager under behandling
                @elseif($mode === 'trash')
                    Papirkurv / Slettede sager
                @elseif($mode === 'kreditor')
                    Sager for {{ $kreditor?->navn ?? 'Kreditor' }}
                @else
                    Sagsoversigt
                @endif
            </h1>

            @if($uiMode === 'full')
                <p class="mt-1 text-sm text-slate-500">
                    Overblik over inkassoportefølje, sagsbehandling, debitorer og GDPR-compliance.
                </p>
            @endif

            @if($selectedKreditor)
                <div class="mt-2 flex items-center gap-1.5 text-xs bg-indigo-50 border border-indigo-100 text-indigo-800 px-2.5 py-1 rounded-lg w-fit shadow-sm">
                    <span>Filtreret på kreditor: <strong class="font-semibold">{{ $selectedKreditor }}</strong></span>
                    <button
                        type="button"
                        wire:click="filterByKreditor(null)"
                        class="ml-1 inline-flex items-center justify-center rounded-md p-0.5 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-900 transition cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif
        </div>

        @if($uiMode === 'full')
            <button
                type="button"
                wire:click="opretnysag"
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow transition hover:bg-indigo-700 cursor-pointer"
            >
                + Opret ny sag
            </button>
        @endif
    </div>

    {{-- STATUS / TILSTAND FANER --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200/80 pb-3">
        <button
            type="button"
            wire:click="setMode('full')"
            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                {{ $mode === 'full' || $mode === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
        >
            <span>Alle sager</span>
        </button>

        <button
            type="button"
            wire:click="setMode('active')"
            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                {{ $mode === 'active' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
        >
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            <span>Aktive sager</span>
        </button>

        <button
            type="button"
            wire:click="setMode('closed')"
            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                {{ $mode === 'closed' || $mode === 'afsluttet' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
        >
            <span>Afsluttede sager</span>
        </button>

        <button
            type="button"
            wire:click="setMode('unread_messages')"
            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                {{ $mode === 'unread_messages' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
        >
            <span>💬 Ulæste beskeder</span>
            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-mono font-bold
                {{ $mode === 'unread_messages' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-700' }}">
                {{ $this->unreadCount }}
            </span>
        </button>

        <button
            type="button"
            wire:click="setMode('trash')"
            class="px-4 py-2 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer
                {{ $mode === 'trash' ? 'bg-rose-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
        >
            <span>🗑 Papirkurv</span>
            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-mono font-bold
                {{ $mode === 'trash' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $this->trashCount }}
            </span>
        </button>
    </div>

    {{-- STATISTICS CARDS --}}
    @if($uiMode === 'full')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div 
                wire:click="setMode('full')"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm cursor-pointer hover:border-slate-400 transition group"
            >
                <p class="text-sm font-medium text-slate-500 group-hover:text-slate-800 transition">Viser i denne fane</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $sagers->total() }}</p>
            </div>

            <div 
                wire:click="setMode('active')"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm cursor-pointer hover:border-indigo-300 transition group"
            >
                <p class="text-sm font-medium text-slate-500 group-hover:text-indigo-600 transition">Totalt antal i tilstand</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $modeCount }}</p>
            </div>

            <div 
                wire:click="setMode('unread_messages')"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm cursor-pointer hover:border-rose-300 hover:shadow-md transition group {{ $mode === 'unread_messages' ? 'ring-2 ring-rose-500/20 border-rose-300' : '' }}"
            >
                <p class="text-sm font-medium text-slate-500 group-hover:text-rose-600 transition flex items-center justify-between">
                    <span>Ulæste beskeder</span>
                    <span class="text-xs text-rose-500 font-semibold group-hover:underline">Vis →</span>
                </p>
                <p class="mt-2 text-3xl font-bold text-rose-600">
                    {{ $this->unreadCount }}
                </p>
            </div>

            <div 
                wire:click="setMode('trash')"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm cursor-pointer hover:border-slate-400 transition group {{ $mode === 'trash' ? 'ring-2 ring-slate-500/20 border-slate-400' : '' }}"
            >
                <p class="text-sm font-medium text-slate-500 group-hover:text-slate-800 transition flex items-center justify-between">
                    <span>I papirkurv</span>
                    <span class="text-xs text-slate-400 font-semibold group-hover:underline">Vis →</span>
                </p>
                <p class="mt-2 text-3xl font-bold text-slate-400 group-hover:text-slate-700 transition">{{ $this->trashCount }}</p>
            </div>
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden relative">
        
        {{-- CONTROLS HEADER --}}
        <div class="flex flex-col gap-5 border-b border-slate-100 p-6 bg-slate-50/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                <div class="text-sm font-semibold text-slate-700 uppercase tracking-wider">
                    Filtrer sager
                </div>
                
                <div class="relative w-full sm:w-96">
                    <input
                        type="search"
                        wire:model.live.debounce.600ms="search"
                        placeholder="Søg sagsnr, debitor eller kreditor..."
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                    >
                    <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- KREDITOR FANER --}}
            @if($uiMode === 'full')
                <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-200/40">
                    <button
                        type="button"
                        wire:click="filterByKreditor(null)"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer
                            {{ $selectedKreditor === null ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                    >
                        <span>Samtlige sager</span>
                    </button>

                    @foreach($kreditors as $kreditorItem)
                        @php $isActive = $selectedKreditor === $kreditorItem->navn; @endphp
                        <button
                            type="button"
                            wire:click="filterByKreditor('{{ addslashes($kreditorItem->navn) }}')"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer
                                {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                        >
                            <span>{{ $kreditorItem->navn }}</span>
                            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-mono font-bold
                                {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-300/70 text-slate-600' }}">
                                {{ $recordsByKreditor[$kreditorItem->navn] ?? 0 }}
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- DATA TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th scope="col" class="px-6 py-4 cursor-pointer select-none hover:text-indigo-600 transition" wire:click="sortBy('sagers.sagsnr')">Sagsnr</th>
                        <th scope="col" class="px-6 py-4">Debitor</th>
                        <th scope="col" class="px-6 py-4">Kreditor</th>
                        <th scope="col" class="px-6 py-4 cursor-pointer select-none hover:text-indigo-600 transition" wire:click="sortBy('modtaget')">Modtaget</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-right w-32">Handlinger</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                @forelse($sagers as $sag)
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-slate-700">{{ $sag->sagsnr ?? '-' }}</span>

                                {{-- 💬 IKON OG BADGE FOR ULÆSTE BESKEDER --}}
                                @if($sag->has_unread_messages)
                                    <span 
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs animate-pulse"
                                        title="Sagen har ulæste beskeder"
                                    >
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-4 0h-2v2h2V9z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Ny besked</span>
                                    </span>
                                @endif
                            </div>

                            @if($sag->trashed())
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 border border-rose-100 px-2 py-0.5 text-xs font-medium text-rose-700">
                                        🗑 Slettet den {{ $sag->deleted_at?->format('d-m-Y') }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $sag->debitor_navn ?? $sag->sagerdebitor->first()?->navn ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $sag->kreditor_navn ?? $sag->sagerkreditor->first()?->navn ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 font-mono text-slate-500">
                            {{ $sag->modtaget ? \Carbon\Carbon::parse($sag->modtaget)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($sag->afsluttet)
                                <span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                    Afsluttet: {{ \Carbon\Carbon::parse($sag->afsluttet)->format('d-m-Y') }}
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700 shadow-sm">
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktiv
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium">
                            @if ($sag->trashed())
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        type="button" 
                                        wire:click="restoreSag({{ $sag->id }})"
                                        class="inline-flex items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm hover:bg-emerald-100 transition cursor-pointer"
                                        title="Gendan sag"
                                    >
                                        ♻️ Gendan
                                    </button>

                                    <button 
                                        type="button" 
                                        wire:click="confirmDelete({{ $sag->id }})"
                                        class="inline-flex items-center justify-center rounded-lg border border-rose-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 shadow-sm hover:bg-rose-50 transition cursor-pointer"
                                        title="Slet permanent"
                                    >
                                        💥 Slet permanent
                                    </button>
                                </div>
                            @elseif ($sag->isEligibleForGdprDeletion())
                                <span class="text-xs text-rose-600 font-semibold px-2 py-1 bg-rose-50 rounded-lg" title="Skal behandles i GDPR retention dashboardet">
                                    GDPR Låst
                                </span>
                            @else
                                <x-table-actions 
                                    :id="$sag->id" 
                                    :editUrl="route('sager.edit', $sag)" 
                                />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m9-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="block text-sm font-semibold text-slate-900">Ingen sager fundet</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50/30">
            {{ $sagers->links() }}
        </div>
    </div>

    {{-- 🟢 DYNAMISK SLETTEMODAL TIL SAGER --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        :title="$mode === 'trash' ? 'Slet sagen permanent?' : 'Send sag i papirkurv?'" 
        :message="$mode === 'trash' ? 'Er du sikker på, at du vil slette denne sag permanent fra databasen? Denne handling kan IKKE fortrydes!' : 'Er du sikker på, at du vil flytte denne sag til papirkurven? Sagen kan gendannes fra papirkurven.'" 
    />

</div>