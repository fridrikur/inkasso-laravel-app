<div class="space-y-6">

    {{-- LIVEWIRE INITIAL / GLOBAL LOADER --}}
    <div wire:loading.delay wire:target="search, filterByKreditor, sortBy, gotoPage, nextPage, previousPage" class="fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 max-w-sm w-full">
            <x-ui-loader type="sager" :count="$modeCount" />
        </div>
    </div>

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                @if($uiMode === 'full')
                    Sagsadministration
                @elseif($mode === 'unhandled')
                    Ubehandlede sager
                @elseif($mode === 'incoming')
                    Nyligt indkomne sager
                @elseif($mode === 'active')
                    Aktive sager
                @elseif($mode === 'unread_messages')
                    Sager med ulæste beskeder
                @elseif($mode === 'live_editing')
                    Sager under behandling
                @elseif($mode === 'trash')
                    Papirkurv / Slettede sager
                @elseif($mode === 'kreditor')
                    Sager for {{ $kreditor?->navn ?? 'Kreditor' }}
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
                        class="ml-1 inline-flex items-center justify-center rounded-md p-0.5 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-900 transition"
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
                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow transition hover:bg-indigo-700"
            >
                + Opret ny sag
            </button>
        @endif
    </div>

    {{-- STATISTICS CARDS --}}
    @if($uiMode === 'full')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Viser i denne fane</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $sagers->total() }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Totalt antal i tilstand</p>
                <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $modeCount }}</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Ulæste beskeder</p>
                <p class="mt-2 text-3xl font-bold text-rose-600">
                    {{ \App\Models\Sager::whereHas('dialogs.messages', fn($q) => $q->whereNull('read_at'))->count() }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">I papirkurv</p>
                <p class="mt-2 text-3xl font-bold text-slate-400">{{ $trashCount }}</p>
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
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none
                            {{ $selectedKreditor === null ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                    >
                        <span>Samtlige sager</span>
                    </button>

                    @foreach($kreditors as $kreditorItem)
                        @php $isActive = $selectedKreditor === $kreditorItem->navn; @endphp
                        <button
                            type="button"
                            wire:click="filterByKreditor('{{ addslashes($kreditorItem->navn) }}')"
                            class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none
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
                            <span class="font-mono text-slate-700">{{ $sag->sagsnr ?? '-' }}</span>
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
                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium space-x-1">
                            <a href="{{ route('sager.edit', $sag) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition">
                                Redigér
                            </a>
                            @if ($sag->isEligibleForGdprDeletion())
                                {{-- GDPR Låste sager har stadig deres specielle status-badge --}}
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-xs text-rose-600 font-semibold px-2 py-1 bg-rose-50 rounded-lg" title="Skal behandles i GDPR retention dashboardet">
                                        GDPR Låst
                                    </span>
                                </td>
                            @else
                                {{-- 🟢 SAMME STILRENE SVG-KNAPPER SOM I RESTEN AF APPLIKATIONEN --}}
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

    {{-- 🟢 GENBRUGELIG SLETTEMODAL TIL SAGER --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Send sag i papirkurv?" 
        message="Er du sikker på, at du vil flytte denne sag til papirkurven? Sagen kan gendannes fra papirkurven." 
    />

</div>