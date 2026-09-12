{{-- YDERSTE WRAPPER MED STABIL LYD- OG EVENT-HANDLER --}}
<div 
    wire:poll.5s="checkTakeoverRequests"
    x-data="{
        playAudio(uri) {
            try {
                const audio = new Audio(uri);
                audio.volume = 0.7;
                audio.play().catch(e => console.log('Audio autoplay restricted'));
            } catch (err) {}
        }
    }"
    @play-knock-sound.window="playAudio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YUaGAACAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA')"
    @play-success-sound.window="playAudio('data:audio/wav;base64,UklGRigFAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQQFAACAgICAgICAgICA')"
    style="background-color: var(--theme-sag-editor-wrapper-bg);"
    class="relative rounded-3xl p-6 sm:p-8 space-y-6 transition-colors duration-200 border border-slate-200/60 shadow-xs"
>
    {{-- SPINNER OVERLAY --}}
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

    {{-- OVERTAGELSES-MODAL (Uden wire:ignore, så knapperne reagerer normalt) --}}
    @if($showTakeoverModal && $pendingRequests->isNotEmpty())
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
            <div class="w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-xl font-bold shrink-0">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Anmodning om overtagelse</h3>
                        <p class="text-xs text-slate-500">En anden medarbejder ønsker at åbne denne sag.</p>
                    </div>
                </div>

                <div class="space-y-2 py-2">
                    @foreach($pendingRequests as $req)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between" wire:key="request-row-{{ $req->id }}">
                            <div>
                                <span class="text-xs font-bold text-slate-800">{{ $req->requester?->name ?? 'Kollega' }}</span>
                                <p class="text-[11px] text-slate-500">Vil overtage redigering af sagen.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    wire:key="reject-btn-{{ $req->id }}"
                                    wire:click="rejectTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer"
                                >
                                    Afvis
                                </button>
                                <button 
                                    type="button" 
                                    wire:key="accept-btn-{{ $req->id }}"
                                    wire:click="acceptTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer"
                                >
                                    Accepter
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif    
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

    {{-- OVERTAGELSES-MODAL (Vises for Medarbejder A når en anden anmoder om adgang) --}}
    @if($showTakeoverModal && $pendingRequests->isNotEmpty())
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
            <div class="w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-xl font-bold shrink-0">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Anmodning om overtagelse</h3>
                        <p class="text-xs text-slate-500">En anden medarbejder ønsker at åbne denne sag.</p>
                    </div>
                </div>

                <div class="space-y-2 py-2">
                    @foreach($pendingRequests as $req)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between" wire:key="request-row-{{ $req->id }}">
                            <div>
                                <span class="text-xs font-bold text-slate-800">{{ $req->requester?->name ?? 'Kollega' }}</span>
                                <p class="text-[11px] text-slate-500">Vil overtage redigering af sagen.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    wire:key="reject-btn-{{ $req->id }}"
                                    wire:click="rejectTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer"
                                >
                                    Afvis
                                </button>
                                <button 
                                    type="button" 
                                    wire:key="accept-btn-{{ $req->id }}"
                                    wire:click="acceptTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer"
                                >
                                    Accepter
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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
            {{-- KAFFEPAUSE KNAP --}}
            @if($sag->exists && !$isLockedByOther)
                <button 
                    type="button" 
                    wire:click="lockcurrentsag" 
                    class="px-3 py-1.5 rounded-xl bg-slate-900/60 hover:bg-slate-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer border border-white/10"
                    title="Lås skærmen mens du holder kaffepause"
                >
                    <span>☕</span> Kaffepause
                </button>
            @endif

            <span class="px-3 py-1.5 rounded-xl bg-white/10 backdrop-blur-md text-xs font-bold">
                Status: {{ $sag->status?->tekst ?? ($sag->exists ? 'Ikke angivet' : 'Kladde') }}
            </span>
        </div>
    </div>

    {{-- AFSLUTTET BANNER --}}
    @if(!empty($form->afsluttet) || ($sag->exists && $sag->afsluttet))
        @include('livewire.sager.partials.afsluttet-banner')
    @endif

    {{-- WRAPPER FOR INDHOLD OG BLUR FOR MEDARBEJDER B --}}
    <div class="relative">

        {{-- LÅSE- OVERLAY FOR MEDARBEJDER B (NÅR SAGEN ER OPTAGET AF EN ANDEN) --}}
        @if($isLockedByOther)
            <div class="absolute inset-0 z-40 bg-white/70 backdrop-blur-md rounded-3xl flex items-start justify-center p-6 pt-12">
                <div class="w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-amber-200 text-center space-y-4">
                    <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl mx-auto shadow-inner">
                        🔒
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Sagen er i øjeblikket låst af en anden</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            <span class="font-bold text-slate-800">{{ $lock['user_name'] ?? 'En kollega' }}</span> redigerer denne sag lige nu. Du kan ikke foretage ændringer.
                        </p>
                    </div>

                    <div>
                        @if($myTakeoverRequest && $myTakeoverRequest->status === 'pending')
                            <span class="w-full py-2.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-xl block border border-amber-200">
                                ⏳ Anmodning om overtagelse er sendt... Venter på svar
                            </span>
                        @elseif($myTakeoverRequest && $myTakeoverRequest->status === 'accepted')
                            <button 
                                type="button" 
                                wire:click="continueAfterTakeoverAccepted" 
                                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs"
                            >
                                Overtagelse accepteret – Klik for at fortsætte
                            </button>
                        @else
                            <button 
                                type="button" 
                                wire:click="requestTakeover" 
                                class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span>🔑 Anmod om overtagelse af sag</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- SELVE INDHOLDET (SLØRET OG SPÆRRET HVIS LÅST AF ANDEN) --}}
        <div class="space-y-6 @if($isLockedByOther) pointer-events-none select-none opacity-40 blur-xs @endif">
            
            {{-- 1.5 TAB NAVIGATION --}}
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
                                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                
                                <span>Gem Sag</span>
                            </button>
                        </div>
                    </form>

                    @if($sag->exists)
                        @role('Admin')
                        <div class="mt-4 flex justify-start">
                            <button
                                type="button"
                                wire:click="confirmDeleteSag"
                                class="px-3 py-1 rounded-xl bg-rose-600/80 hover:bg-rose-600 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer"
                            >
                                <span>🗑 Slet sag</span>
                            </button>
                        </div>
                        @endrole
                    @endif

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

                    {{-- 3. BESKED-SLETTEMODAL --}}
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

        </div>
    </div>

    {{-- MODAL: PÅMINDELSE OM AFSLUTNINGSDATO ELLER -ÅRSAG --}}
    @if($showAfsluttetDateReminder)
        @include('livewire.sager.partials.afslutning-modal')
    @endif

    {{-- KAFFEPAUSE / LÅSESKÆRM OVERLAY --}}
    @if($currentsagLocked)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div class="w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl text-center space-y-4 border border-slate-100">
                <div class="mx-auto w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-2xl shadow-inner">
                    ☕
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Skærmen er låst (Kaffepause)</h3>
                    <p class="text-xs text-slate-500 mt-1">Indtast systemets globale unlock-kode for at låse op og fortsætte.</p>
                </div>

                <div class="space-y-3 pt-2">
                    <input 
                        type="password" 
                        wire:model="unlockCode" 
                        placeholder="Indtast låsekode..." 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-center outline-none focus:border-indigo-500 font-mono"
                    >
                    <button 
                        type="button" 
                        wire:click="unlockcurrentsag"
                        class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs cursor-pointer transition"
                    >
                        Lås op
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>