<div x-data="{ showPreview: false }" class="max-w-4xl mx-auto space-y-6">

    {{-- FANER FOR DROPDOWNS --}}
    <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between gap-2 overflow-x-auto">
        <div class="flex items-center gap-1 overflow-x-auto">
            @foreach([
                'status'     => ['label' => 'Statusser', 'icon' => '🏷️'],
                'ktr'        => ['label' => 'KTR Koder', 'icon' => '📌'],
                'afslutning' => ['label' => 'Afslutninger', 'icon' => '🏁'],
                'bemaerkning'=> ['label' => 'Bemærkninger', 'icon' => '💬'],
                'udlaeg'     => ['label' => 'Udlægstyper', 'icon' => '💰']
            ] as $key => $tab)
                <button 
                    wire:click="setTab('{{ $key }}')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 {{ $activeTab === $key ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                >
                    <span>{{ $tab['icon'] }}</span>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- TOGGLE KNAP TIL PLACERINGS-MOCKUP --}}
        <button 
            type="button" 
            @click="showPreview = !showPreview" 
            class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold border transition cursor-pointer shrink-0"
            :class="showPreview ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'"
        >
            <span>👁️</span>
            <span x-text="showPreview ? 'Skjul placering' : 'Vis placering i sagseditor'"></span>
            <svg :class="showPreview ? 'rotate-180' : ''" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    {{-- LYS PLACERINGS-INDIKATOR --}}
    <div 
        x-show="showPreview" 
        x-cloak 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="bg-indigo-50/40 border border-indigo-100 p-5 rounded-2xl space-y-3"
    >
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-900">
                <span>📍</span>
                <span>Placering i Sagsredigeringen</span>
            </div>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-lg bg-white border border-indigo-200 text-indigo-700">
                Felt: {{ $currentTitle }}
            </span>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            @switch($activeTab)
                @case('status') <strong>Status</strong> overskriver feltet under Række 2 i sagseditorens generelle sektion. @break
                @case('ktr') <strong>KTR Koden (Kontrakttype)</strong> anvendes i Række 4 i sagseditorens generelle sektion. @break
                @case('udlaeg') <strong>Udlæg bilbogen</strong> anvendes i Række 5 i sagseditorens generelle sektion. @break
                @case('afslutning') <strong>Afslutning</strong> er placeret nederst til højre i Række 5. @break
                @case('bemaerkning') <strong>Bemærkning</strong> er placeret nederst til højre direkte under Afslutning. @break
            @endswitch
        </p>

        {{-- LYS MOCKUP GRID --}}
        <div class="bg-white p-3.5 rounded-xl border border-indigo-100/80 shadow-sm space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Sagsredigering Oversigt
            </div>

            <div class="grid grid-cols-4 gap-1.5 text-[11px] font-sans text-slate-400">
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Sagsnr</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Kreditor nr</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Kreditor / Firma</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Modtaget</div>

                <div class="p-1.5 rounded transition-all duration-300 {{ $activeTab === 'status' ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-300 shadow-sm' : 'bg-slate-50 border border-slate-100' }}">
                    Status
                </div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Seneste rapport</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Sagsbehandler</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Afsluttet</div>

                <div class="col-span-2 p-1.5 rounded bg-slate-50 border border-slate-100">Aktiv</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Stelnummer</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Betalt</div>

                <div class="p-1.5 rounded transition-all duration-300 {{ $activeTab === 'ktr' ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-300 shadow-sm' : 'bg-slate-50 border border-slate-100' }}">
                    Kontrakttype (KTR)
                </div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Mdl. ydelse</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Konsulent</div>
                <div class="p-1.5 rounded bg-slate-50 border border-slate-100">Faktureret</div>

                <div class="p-1.5 rounded transition-all duration-300 {{ $activeTab === 'udlaeg' ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-300 shadow-sm' : 'bg-slate-50 border border-slate-100' }}">
                    Udlæg bilbogen
                </div>
                <div class="p-1.5 rounded bg-slate-50/50 border border-slate-100 opacity-40">-</div>
                <div class="p-1.5 rounded bg-slate-50/50 border border-slate-100 opacity-40">-</div>

                <div class="space-y-1">
                    <div class="p-1 rounded transition-all duration-300 {{ $activeTab === 'afslutning' ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-300 shadow-sm' : 'bg-slate-50 border border-slate-100' }}">
                        Afslutning
                    </div>
                    <div class="p-1 rounded transition-all duration-300 {{ $activeTab === 'bemaerkning' ? 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-300 shadow-sm' : 'bg-slate-50 border border-slate-100' }}">
                        Bemærkning
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    {{-- DATA TABLE --}}
    <x-data-table 
        :title="$currentIcon . ' Administrer ' . $currentTitle" 
        description="Disse muligheder fremgår i dropdown-vælgerne på sagsredigeringen."
        :headers="['Tekst / Navn', 'Forkortelse / Kode', '']"
        :items="$items"
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <button 
                    type="button" 
                    wire:click="confirmPurgeCache" 
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer shadow-sm"
                    title="Ryd gemt cache for alle dropdowns"
                >
                    <span>🔄</span>
                    <span>Ryd Cache</span>
                </button>

                <button 
                    type="button" 
                    wire:click="openCreateModal" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Opret ny {{ strtolower($currentTitle) }}</span>
                </button>
            </div>
        </x-slot:action>

        @forelse ($items as $item)
            <tr wire:key="item-{{ $item->id }}" class="hover:bg-slate-50/50 transition">
                <td class="px-6 py-4 font-semibold text-slate-900">
                    <button 
                        type="button"
                        wire:click="openEditModal({{ $item->id }})" 
                        class="hover:text-indigo-600 transition flex items-center gap-2 group text-left cursor-pointer"
                    >
                        <span>{{ $item->tekst }}</span>
                    </button>
                </td>
                <td class="px-6 py-4">
                    @if(!empty($item->forkortelse))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $item->forkortelse }}
                        </span>
                    @else
                        <span class="text-xs text-slate-400 italic">-</span>
                    @endif
                </td>
                
                {{-- 🟢 RETTELSE: Handlinger skal ligge inde i en <td> for at matche kolonnen --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <x-table-actions :id="$item->id" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-xs">
                    Ingen registrerede muligheder fundet for {{ $currentTitle }}.
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- MODAL 1: OPRET / REDIGER --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div wire:click="closeFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">{{ $currentIcon }}</span>
                            <h3 class="text-base font-bold text-slate-900">{{ $editingId ? 'Rediger ' . $currentTitle : 'Opret ' . $currentTitle }}</h3>
                        </div>
                        <button type="button" wire:click="closeFormModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tekst / Navn</label>
                                <input type="text" wire:model="tekst" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Forkortelse / Kode</label>
                                <input type="text" wire:model="forkortelse" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-mono outline-none uppercase focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
                            </div>
                        </div>

                        <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" wire:click="closeFormModal" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs cursor-pointer">Annuller</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-xs cursor-pointer shadow-sm">Gem ændringer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- 🟢 SLETTEMODAL TIL TABELELEMENTER --}}
    {{-- MODAL TIL SLETNING --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Slet element?" 
        message="Er du sikker på, at du vil slette dette element? Handlingen kan ikke fortrydes." 
        wire:click="confirmDelete" 
        @confirm="$wire.confirmDelete()"
        @cancel="$wire.cancelDelete()"
    />

    {{-- 🟢 MODAL TIL PURGE CACHE --}}
    @if($showPurgeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div wire:click="closeFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
            
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-amber-50 text-amber-600 font-bold text-sm shadow-sm">🔄</span>
                            <h3 class="text-base font-bold text-slate-900">Ryd dropdown-cache?</h3>
                        </div>
                        <button type="button" wire:click="$set('showPurgeModal', false)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 text-center space-y-2">
                        <p class="text-sm font-semibold text-slate-800">
                            Vil du tvinge genindlæsning af alle dropdown-data i sagseditoren?
                        </p>
                    </div>

                    <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" wire:click="$set('showPurgeModal', false)" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs cursor-pointer">Annuller</button>
                        <button type="button" wire:click="purgeCache" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-xs cursor-pointer shadow-sm">Ja, ryd cache nu</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>