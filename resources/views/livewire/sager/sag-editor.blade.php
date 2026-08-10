<div class="relative min-h-[400px]">

    {{-- TOP BAR: FANER --}}
    <div class="mb-6">
        <livewire:sager.sag-tabs :sag="$sag" :activeTab="$activeTab" :key="'sag-tabs-'.($sag->id ?? 'new')" />
    </div>

    {{-- VISES KUN HVIS SAGEN REELT ER GEMT I DATABASEN SOM AFSLUTTET --}}
    @if($sag->exists && $sag->afsluttet)
        <div class="mb-4 p-3.5 rounded-2xl border bg-slate-100/80 border-slate-200 text-slate-700 text-xs font-medium flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="text-base">🏁</span>
                <div>
                    <strong>Denne sag er afsluttet</strong> pr. 
                    <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($sag->afsluttet)->format('d-m-Y') }}</span>
                    @if($sag->sagerAfslutning->first())
                        • Årsag: <span class="italic text-slate-800">{{ $sag->sagerAfslutning->first()->tekst }}</span>
                    @endif
                </div>
            </div>
            <span class="text-[10px] bg-slate-200 px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider text-slate-600">
                Afsluttet sag
            </span>
        </div>
    @endif

    {{-- GDPR ADVARSEL BANNER I EDITOR --}}
    @if ($this->isExpiringSoon)
        <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-white shadow-sm flex-shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">
                        GDPR BEMÆRKNING: Sagen nærmer sig 5-års forældelse
                    </h3>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Sagen blev afsluttet {{ $this->sag->afsluttet?->format('d-m-Y') }} og har
                        <strong class="font-semibold">{{ $this->sag->gdpr_days_left }} dage tilbage</strong> før den overskrider 5-års grænsen for anonymisering.
                    </p>
                </div>
            </div>

            <span class="rounded-xl bg-amber-200/60 px-3 py-1.5 text-xs font-semibold text-amber-900 whitespace-nowrap">
                Udløber {{ $this->sag->gdpr_deadline?->format('d-m-Y') }}
            </span>
        </div>
    @endif

    {{-- LOCK STATUS BAR --}}
    @if($lockState === 'foreign' || $this->lockState === 'foreign')
        <div class="mb-4 p-3 rounded-xl border bg-red-50 border-red-200 text-red-700 text-xs font-medium flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span>🔒 Låst af <strong>{{ $lock['user_name'] ?? ($this->currentLock?->user?->name ?? 'en anden bruger') }}</strong></span>
                
                @if($myTakeoverRequest?->status === 'pending')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                        ⏳ Anmodning afventer godkendelse...
                    </span>
                @endif
            </div>

            <div>
                @if($myTakeoverRequest?->status === 'pending')
                    <button 
                        disabled 
                        class="px-3 py-1.5 bg-gray-300 text-gray-600 font-bold rounded-lg text-xs cursor-not-allowed opacity-75"
                    >
                        Anmodning sendt
                    </button>
                @else
                    <button 
                        type="button"
                        wire:click="requestTakeover" 
                        wire:loading.attr="disabled"
                        class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white font-bold rounded-lg transition shadow-sm cursor-pointer flex items-center gap-2"
                    >
                        <svg wire:loading wire:target="requestTakeover" class="animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Anmod om overtagelse</span>
                    </button>
                @endif
            </div>
        </div>
    @endif
    
    {{-- LÅSE- OG HANDLINGSKNAPPER --}}
    <div class="flex items-center gap-2 mb-6">
        @if($this->lockState === 'mine')
            {{-- 🔒 LÅS SKÆRM KNAP --}}
            <button
                type="button"
                wire:click="lockcurrentsag"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-6a4 4 0 100-8 4 4 0 000 8zm6 6a6 6 0 00-12 0"/>
                </svg>
                <span>Lås skærm</span>
            </button>

            {{-- 🔓 FRIGIV SAG --}}
            <button
                type="button"
                wire:click="unlockSag"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
            >
                Frigiv sag
            </button>

            @if($this->pendingRequests->isNotEmpty())
                <button
                    type="button"
                    wire:click="$toggle('showTakeoverModal')"
                    class="px-4 py-2 bg-amber-100 border border-amber-300 text-amber-900 font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                >
                    Overtagelses-anmodninger ({{ $this->pendingRequests->count() }})
                </button>
            @endif
        @endif
    </div>

    {{-- TAB INDHOLD --}}
    
    {{-- FANE 1: SAGSSTAMME --}}
    @if($activeTab === 'stamdata')
        <div wire:key="tab-stamdata-{{ $sag->id ?? 'new' }}" class="transition-opacity duration-300">
            <form wire:submit.prevent="save" class="{{ $this->lockState === 'foreign' ? 'opacity-40 pointer-events-none blur-sm' : '' }}">
                
                @include('livewire.sager.partials.form-fields')

                <div class="flex justify-end mt-4">
                    <button 
                        type="button" 
                        wire:click="save"
                        wire:loading.attr="disabled"
                        :disabled="$wire.savedRecently"
                        class="min-w-[140px] h-10 px-6 font-bold text-xs rounded-xl transition-all duration-200 shadow-sm cursor-pointer inline-flex items-center justify-center gap-2 relative overflow-hidden text-white
                            {{ $savedRecently ? 'bg-emerald-600 hover:bg-emerald-600' : 'bg-indigo-600 hover:bg-indigo-700' }} 
                            disabled:opacity-90 disabled:cursor-not-allowed"
                    >
                        {{-- 1. TILSTAND: GEMMER (Spinner) --}}
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Gemmer...</span>
                        </span>

                        {{-- 2. TILSTAND: GEMT SUCCESFULDT (Grøn knap) --}}
                        @if($savedRecently)
                            <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-1.5 animate-pulse">
                                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Gemt!</span>
                            </span>
                        {{-- 3. TILSTAND: STANDARD (Individuel gem-knap) --}}
                        @else
                            <span wire:loading.remove wire:target="save">
                                Gem sag
                            </span>
                        @endif
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- FANE 2: BREVE --}}
    @if($activeTab === 'breve')
        <div wire:key="tab-breve-{{ $sag->id }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm transition-opacity duration-300">
            @if($sag->exists)
                @livewire('sager.merge-brev', ['sag' => $sag], key('merge-brev-component-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 font-medium">
                    Du skal gemme sagen først, før du kan oprette og brevflette skrivelser.
                </div>
            @endif
        </div>
    @endif

    {{-- FANE 3: DIALOG & NOTER --}}
    @if($activeTab === 'dialog')
        <div wire:key="tab-dialog-{{ $sag->id }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6 transition-opacity duration-300" x-data="{ subTab: 'klientinformation' }">
            <div class="border-b border-slate-100 pb-3 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <button 
                        type="button" 
                        @click="subTab = 'klientinformation'" 
                        :class="subTab === 'klientinformation' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
                        class="px-3.5 py-1.5 rounded-xl text-xs border transition flex items-center gap-2 cursor-pointer"
                    >
                        <span>💬 Klientinformation</span>
                    </button>

                    <button 
                        type="button" 
                        @click="subTab = 'historik'" 
                        :class="subTab === 'historik' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
                        class="px-3.5 py-1.5 rounded-xl text-xs border transition flex items-center gap-2 cursor-pointer"
                    >
                        <span>📜 Historik & Noter</span>
                    </button>

                    <button 
                        type="button" 
                        @click="subTab = 'bogholderi'" 
                        :class="subTab === 'bogholderi' ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-bold' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'"
                        class="px-3.5 py-1.5 rounded-xl text-xs border transition flex items-center gap-2 cursor-pointer"
                    >
                        <span>🏷️ Bogholderi Noter</span>
                    </button>
                </div>

                <span class="text-xs text-slate-400 font-medium">Sag #{{ $sag->sagsnr }}</span>
            </div>

            <div x-show="subTab === 'klientinformation'">
                @livewire('sager.klientinformation', ['sag' => $sag], key('dialog-klientinfo-'.$sag->id))
            </div>

            <div x-show="subTab === 'historik'">
                @livewire('sager.historik', ['sag' => $sag], key('dialog-historik-'.$sag->id))
            </div>

            <div x-show="subTab === 'bogholderi'">
                @livewire('sager.bogholderi', ['sag' => $sag], key('dialog-bogholderi-'.$sag->id))
            </div>
        </div>
    @endif

    {{-- FANE 4: BOGHOLDERI --}}
    @if($activeTab === 'bogholderi')
        <div wire:key="tab-bogholderi-{{ $sag->id }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm transition-opacity duration-300">
            @if($sag->exists)
                @livewire('sager.bogholderi', ['sag' => $sag], key('bogholderi-component-'.$sag->id))
            @else
                <div class="p-8 text-center text-slate-400 font-medium">
                    Du skal gemme sagen først for at tilgå bogholderiet.
                </div>
            @endif
        </div>
    @endif

    {{-- MODALS --}}
    @if($currentsagLocked)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-sm text-center border border-slate-100">
                <div class="mb-4 flex justify-center">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xl">
                        🔒
                    </div>
                </div>
                <h2 class="text-lg font-bold text-slate-900 mb-1">Skærm låst</h2>
                <p class="text-xs text-slate-500 mb-5">Indtast oplåsningskode for at fortsætte</p>
                <input 
                    type="password" 
                    wire:model.defer="unlockCode" 
                    wire:keydown.enter="unlockcurrentsag" 
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-center mb-3 focus:outline-none focus:border-indigo-500 text-sm" 
                    placeholder="Kode" 
                />
                <button 
                    wire:click="unlockcurrentsag" 
                    class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                >
                    Lås op
                </button>
            </div>
        </div>
    @endif

    @if($showTakeoverModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md">
                <h2 class="font-bold text-slate-900 mb-4 text-sm">Overtagelses-anmodninger</h2>
                
                @forelse ($pendingRequests ?? [] as $request)
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl mb-2 border border-slate-100">
                        <div class="text-xs font-medium text-slate-700">
                            {{ $request->requester?->name ?? ('Bruger #' . $request->requested_by) }}
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="acceptTakeover({{ $request->id }})" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition">Accepter</button>
                            <button wire:click="rejectTakeover({{ $request->id }})" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-lg transition">Afvis</button>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-400">Ingen aktive anmodninger</div>
                @endforelse

                <button wire:click="$set('showTakeoverModal', false)" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl w-full transition">Luk</button>
            </div>
        </div>
    @endif

    {{-- MODAL: PÅMINDELSE OM AFSLUTNINGSDATO --}}
    @if($showAfsluttetDateReminder)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4"
            x-data
            @keydown.escape.window="$wire.set('showAfsluttetDateReminder', false)"
        >
            <div class="bg-white rounded-3xl shadow-2xl p-6 w-full max-w-md border border-slate-100 space-y-4">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                        📅
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Mangler afslutningsdato</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Du har valgt en afslutningsårsag, men feltet <strong>Afsluttet (dato)</strong> er tomt.
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    Vil du udfylde feltet <strong>Afsluttet</strong> med dags dato ({{ now()->format('d-m-Y') }})?
                </p>

                <div class="flex justify-end gap-2 pt-2">
                    {{-- ESC / LUK KNAP --}}
                    <button 
                        type="button" 
                        wire:click="$set('showAfsluttetDateReminder', false)"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer"
                    >
                        Spring over (Esc)
                    </button>

                    {{-- UDFYLD MED DAGS DATO --}}
                    <button 
                        type="button" 
                        wire:click="applyTodayAfsluttetDate"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                    >
                        Udfyld dags dato
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- 🟢 STILLE BAGGRUNDS-POLLER (Uden flimmer) --}}
    @if($sag?->exists)
        <div 
            wire:poll.5s="checkTakeoverRequests" 
            wire:ignore
            class="hidden"
        ></div>
    @endif

</div>