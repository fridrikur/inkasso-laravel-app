{{-- YDERSTE WRAPPER MED DYNAMISK SIDEBAGGRUND --}}
<div 
    style="background-color: var(--theme-sag-editor-wrapper-bg);"
    class="relative rounded-3xl p-6 sm:p-8 space-y-6 transition-colors duration-200 border border-slate-200/60 shadow-xs"
>
    {{-- SPINNER OVERLAY (Vises KUN ved indlæsning og fane-skift, helt uden blur) --}}
    <div 
        wire:loading.flex
        wire:target="setTab"
        class="absolute inset-0 bg-white/40 z-50 items-center justify-center rounded-3xl transition-all"
        style="display: none;"
    >
        <div class="bg-white px-5 py-3 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 text-slate-800 text-xs font-bold">
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Indlæser sagen...</span>
        </div>
    </div>

    {{-- 1. SAG EDITOR HEADER --}}
    <div 
        style="background-color: var(--theme-sag-editor-header);"
        class="rounded-2xl p-6 text-white shadow-sm transition-colors duration-200 flex items-center justify-between"
    >
        <div>
            <span class="text-xs font-bold uppercase tracking-wider opacity-80">Sagsbehandling</span>
            <h1 class="text-xl font-bold tracking-tight mt-0.5">
                {{ $sag->exists ? 'Redigér Sag #' . $sag->sagsnr : 'Opret Ny Sag' }}
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-xl bg-white/10 backdrop-blur-md text-xs font-bold">
                Status: {{ $sag->status?->navn ?? 'Kladde' }}
            </span>
        </div>
    </div>

    {{-- AFSLUTTET BANNER --}}
    @if(!empty($form->afsluttet) || ($sag->exists && $sag->afsluttet))
        @include('livewire.sager.partials.afsluttet-banner')
    @endif

    {{-- 1.5 TAB NAVIGATION (BRUGER SAGTABS) --}}
    @livewire('sager.sag-tabs', ['sag' => $sag, 'activeTab' => $activeTab], key($sag?->id ?? 'new'))

    {{-- 2. HOVED-INDHOLD (DYNAMISK BASERET PÅ TAB) --}}
    <div 
        style="background-color: var(--theme-sag-editor-bg);"
        class="rounded-2xl border border-slate-200/80 p-6 shadow-sm transition-colors duration-200"
    >
        @if($activeTab === 'stamdata')
            <form wire:submit.prevent="save">
                @include('livewire.sager.partials.form-fields')

                <div class="flex justify-end mt-6 pt-4 border-t border-slate-100">
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        wire:target="save"
                        style="background-color: var(--theme-primary);"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer hover:opacity-90 disabled:opacity-75"
                    >
                        {{-- Spinner vises KUN når save() kører --}}
                        <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span>Gem Sag</span>
                    </button>
                </div>
            </form>
            {{-- Sæt denne knap ind i headeren hvor Slet-knappen skal være (kun for Admin) --}}
            @if($sag->exists)
                @role('Admin')
                <button
                    type="button"
                    wire:click="confirmDeleteSag"
                    class="px-3 py-1 rounded-xl bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer"
                >
                    <span>🗑 Slet sag</span>
                </button>
                @endrole
            @endif

            {{-- Indsæt disse to modaler nederst i filen --}}

            {{-- 1. VALGKOMPONENT: PAPIRKURV ELLER PERMANENT --}}
            @if($showDeleteOptionsModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                                🗑️
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Slet sag</h3>
                                <p class="text-xs text-slate-500">Vælg hvordan sagen skal slettes.</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600">
                            Vil du flytte sag <span class="font-bold text-slate-900">#{{ $sag->sagsnr }}</span> til papirkurven, eller ønsker du at slette den permanent?
                        </p>

                        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">
                            <button
                                wire:click="cancelDeleteSag"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Annuller
                            </button>
                            
                            <button
                                wire:click="moveToTrash"
                                class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-amber-500 transition cursor-pointer"
                            >
                                Smid i papirkurv
                            </button>

                            <button
                                wire:click="promptPermanentDelete"
                                class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 transition cursor-pointer"
                            >
                                Slet permanent...
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. LÅSESKÆRM MODAL TIL PERMANENT SLETNING --}}
            @if($showPermanentDeleteUnlockModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                                🔒
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Bekræft permanent sletning</h3>
                                <p class="text-xs text-slate-500">Indtast systemets låsekode for at fortsætte.</p>
                            </div>
                        </div>

                        <p class="text-sm text-slate-600">
                            Denne handling kan <span class="font-bold text-rose-600">ikke</span> fortydes. Alle relationer, dokumenter og data vil blive fjernet for evigt.
                        </p>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Låsekode</label>
                            <input 
                                type="password" 
                                wire:model="permanentDeleteUnlockCode" 
                                placeholder="Indtast låsekode..." 
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-rose-500"
                            >
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button
                                wire:click="cancelDeleteSag"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Annuller
                            </button>
                            
                            <button
                                wire:click="executePermanentDelete"
                                class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 transition cursor-pointer"
                            >
                                Bekræft og slet permanent
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            {{-- 🟢 3. BESKED-SLETTEMODAL (Til Historik/Noter) --}}
    @if($showDeleteMessageModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button 
                    type="button" 
                    wire:click="$set('showDeleteMessageModal', false)" 
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer"
                >
                    &times;
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Flyt besked til papirkurv?
                        </h3>
                        <p class="text-xs text-slate-500">
                            Beskeden fjernes, men kan gendannes, hvis du fortryder.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteMessageModal', false)" 
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    <button 
                        type="button" 
                        wire:click="executeDeleteMessage" 
                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer"
                    >
                        Flyt til papirkurv
                    </button>
                </div>
            </div>
        </div>
    @endif

</div> {{-- Lukning af den yderste wrapper --}}

        @elseif($activeTab === 'breve')
            @if($sag->exists)
                @livewire('sager.merge-brev', ['sag' => $sag], key('merge-brev-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
                    Du skal gemme sagen som kladde, før du kan oprette breve.
                </div>
            @endif

        @elseif($activeTab === 'klientinformation')
            @if($sag->exists)
                @livewire('sager.klientinformation', ['sag' => $sag], key('klientinfo-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
                    Du skal gemme sagen som kladde, før du kan tilføje klientinformation.
                </div>
            @endif

        @elseif($activeTab === 'historik')
            @if($sag->exists)
                @livewire('sager.historik', ['sag' => $sag], key('historik-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
                    Historik er tilgængelig, når sagen er oprettet.
                </div>
            @endif

        @elseif($activeTab === 'bogholderi')
            @if($sag->exists)
                @livewire('sager.bogholderi', ['sag' => $sag], key('bogholderi-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
                    Bogholderi kan tilgås, når sagen er oprettet.
                </div>
            @endif

        @elseif($activeTab === 'dokumenter')
            @if($sag->exists)
                <div class="space-y-6">
                    {{-- Upload Formular --}}
                    @role('Admin|Medarbejder|Kreditor')
                    <form wire:submit.prevent="uploadDokument" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center gap-4">
                            <input type="file" wire:model="newDokument" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                Upload Dokument
                            </button>
                        </div>
                        @error('newDokument') <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </form>
                    @endrole

                    {{-- Liste over dokumenter --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">
                        @forelse($sag->dokumenter()->latest()->get() as $dok)
                            <div class="p-4 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-slate-800 text-xs">{{ $dok->file_name }}</div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ number_format($dok->file_size / 1024, 2) }} KB
                                        – {{ $dok->uploaded_date->format('d-m-Y H:i') }}
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <a href="{{ route('sager.dokumenter.download', [$sag, $dok]) }}"
                                    class="text-indigo-600 hover:underline text-xs font-bold">
                                        Download
                                    </a>

                                    @role('Admin|Medarbejder')
                                    <button type="button" wire:click="deleteDokument({{ $dok->id }})" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                        Slet
                                    </button>
                                    @endrole
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs">
                                Ingen dokumenter tilknyttet denne sag endnu.
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
                    Du skal gemme sagen som kladde, før du kan uploade dokumenter.
                </div>
            @endif
        @endif
    </div>

    {{-- MODAL: PÅMINDELSE OM AFSLUTNINGSDATO ELLER -ÅRSAG --}}
    @if($showAfsluttetDateReminder)
        @include('livewire.sager.partials.afslutning-modal')
    @endif

</div>