<div class="space-y-6">

    {{-- STATISTIK KORT --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kreditorer i alt</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $kreditorer->count() }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Med aktive sager</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $kreditorer->where('sager_count', '>', 0)->count() }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Uden sager</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $kreditorer->where('sager_count', 0)->count() }}
            </p>
        </div>
    </div>

    {{-- DATA TABEL KORT --}}
    <x-data-table 
        title="Kreditorer" 
        description="Oversigt og administration af kreditorer og deres tilknyttede sager."
        :headers="['Navn / Status', 'Oprettet']"
        :items="$kreditorer"
        wire:model.live="search"
    >
        <x-slot:action>
            <button 
                type="button" 
                wire:click="opretnykreditor" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Ny kreditor</span>
            </button>
        </x-slot:action>

        @forelse($kreditorer as $kreditor)
            <tr wire:key="kreditor-{{ $kreditor->id }}" class="hover:bg-slate-50/60 transition duration-150">
                
                {{-- NAVN OG SAGSBADGE --}}
                <td class="px-6 py-4">
                    <div class="font-semibold text-slate-900">
                        {{ $kreditor->navn }}
                    </div>

                    <div class="mt-1 flex items-center gap-2">
                        @if($kreditor->sager_count > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200/60 text-[11px] font-semibold">
                                {{ $kreditor->sager_count }} aktive sager
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[11px] font-semibold">
                                Ingen sager
                            </span>
                        @endif

                        <span class="text-xs text-slate-400 font-mono">#{{ $kreditor->id }}</span>
                    </div>
                </td>

                {{-- OPRETTET DATO --}}
                <td class="px-6 py-4 text-xs font-mono text-slate-500 whitespace-nowrap">
                    {{ $kreditor->created_at?->format('d-m-Y') ?? '-' }}
                </td>

                {{-- 🟢 STRØMLINET HANDLINGER VIA X-TABLE-ACTIONS --}}
                <x-table-actions 
                :id="$kreditor->id" 
                :editUrl="route('kreditor.manage', ['kreditor' => $kreditor])" 
                deleteAction="requestDelete"
            >
                @if($kreditor->sager_count > 0)
                    <button
                        type="button"
                        wire:click="openTransferModal({{ $kreditor->id }})"
                        class="inline-flex items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-semibold shadow-sm transition border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 cursor-pointer"
                        title="Overfør sager uden at slette kreditoren"
                    >
                        <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Overfør
                    </button>
                @endif
            </x-table-actions>

            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-12 text-center text-slate-400 text-xs">
                    Ingen kreditorer fundet.
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- MODAL 1: SLETTEMODAL (INKL. OVERFØRSEL & SIKKERHEDSKODE) --}}
    @if($showDeleteModal && $kreditorToDelete)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150">
            
            {{-- HEADER --}}
            <div class="flex items-center gap-3">
                <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Slet kreditor</h3>
                    <p class="text-xs font-semibold text-slate-500 font-mono">{{ $kreditorToDelete->navn }}</p>
                </div>
            </div>

            {{-- 🟢 TILSTAND A: DER ER SAGER --}}
            @if($kreditorToDelete->sager_count > 0)
                <div class="p-4 bg-amber-50 border border-amber-200/80 rounded-2xl space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-900 uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Kreditoren har {{ $kreditorToDelete->sager_count }} aktive sager</span>
                    </div>
                    <p class="text-xs text-amber-800">
                        Vælg venligst den kreditor, som sagerne skal overføres til, og indtast sikkerhedskoden før sletning:
                    </p>

                    <select
                        wire:model="transferToKreditorId"
                        class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 shadow-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-none"
                    >
                        <option value="">-- Vælg modtager-kreditor --</option>
                        @foreach($transferTargets as $target)
                            <option value="{{ $target->id }}">{{ $target->navn }}</option>
                        @endforeach
                    </select>
                    @error('transferToKreditorId')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SIKKERHEDSKODE KUN VED SAGER --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                        Indtast global sikkerhedskode
                    </label>
                    <input
                        type="password"
                        wire:model="securityCode"
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-none"
                    >
                    @error('securityCode')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

            {{-- 🟢 TILSTAND B: 0 SAGER --}}
            @else
                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-600 leading-relaxed">
                    Er du sikker på, at du vil slette <strong class="font-semibold text-slate-900">{{ $kreditorToDelete->navn }}</strong>? Kreditoren har ingen sager og vil blive slettet permanent med det samme.
                </div>
            @endif

            {{-- ACTIONS FOOTER --}}
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button
                    type="button"
                    wire:click="cancelDelete"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer"
                >
                    Annuller
                </button>

                <button
                    type="button"
                    wire:click="confirmDelete"
                    class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-sm transition cursor-pointer"
                >
                    @if($kreditorToDelete->sager_count > 0)
                        Bekræft overførsel & slet
                    @else
                        Slet permanent
                    @endif
                </button>
            </div>

        </div>
    </div>
@endif

    {{-- MODAL 2: STANDALONE OVERFØR SAGER (UDEN SLETNING) --}}
    @if($showStandaloneTransferModal && $kreditorToTransferFrom)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Overfør sager</h3>
                        <p class="text-xs font-semibold text-slate-500 font-mono">{{ $kreditorToTransferFrom->navn }} ({{ $kreditorToTransferFrom->sager_count }} sager)</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600">
                    Vælg den kreditor, som alle sager fra <strong class="font-semibold text-slate-900">{{ $kreditorToTransferFrom->navn }}</strong> skal overføres til. Kreditoren vil <strong>ikke</strong> blive slettet.
                </p>

                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Modtager-kreditor
                    </label>
                    <select
                        wire:model="transferToKreditorId"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                    >
                        <option value="">-- Vælg modtager-kreditor --</option>
                        @foreach($transferTargets as $target)
                            <option value="{{ $target->id }}">{{ $target->navn }}</option>
                        @endforeach
                    </select>
                    @error('transferToKreditorId')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="closeTransferModal"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button
                        type="button"
                        wire:click="executeStandaloneTransfer"
                        class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition cursor-pointer"
                    >
                        Overfør sager nu
                    </button>
                </div>
            </div>
        </div>
    @endif

@livewire('kreditor.kreditor-form-modal')
</div>